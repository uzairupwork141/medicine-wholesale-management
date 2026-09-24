<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $q = Sale::with(['customer', 'user'])
            ->latest('sale_date')
            ->latest('id');

        if ($request->filled('search')) {
            $s = trim($request->string('search')->toString());

            $q->where(function ($query) use ($s) {
                $query->where('invoice_no', 'like', "%{$s}%")
                    ->orWhereHas('customer', function ($customer) use ($s) {
                        $customer->where('business_name', 'like', "%{$s}%")
                            ->orWhere('customer_code', 'like', "%{$s}%")
                            ->orWhere('phone', 'like', "%{$s}%");
                    });
            });
        }

        if ($request->filled('from_date')) {
            $q->whereDate('sale_date', '>=', $request->date('from_date'));
        }

        if ($request->filled('to_date')) {
            $q->whereDate('sale_date', '<=', $request->date('to_date'));
        }

        $sales = $q->paginate(20)->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function create(): View
    {
        return view('sales.create');
    }

    public function searchCustomers(Request $request): JsonResponse
    {
        $s = trim($request->string('q')->toString());

        if ($s === '') {
            return response()->json([]);
        }

        $customers = Customer::query()
            ->where('deleted', 0)
            ->where('is_active', 1)
            ->where(function ($q) use ($s) {
                $q->where('business_name', 'like', "%{$s}%")
                    ->orWhere('customer_code', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            })
            ->orderBy('business_name')
            ->limit(15)
            ->get(['id', 'customer_code', 'business_name', 'phone']);

        return response()->json($customers);
    }

    public function searchMedicines(Request $request): JsonResponse
    {
        $s = trim($request->string('q')->toString());

        if ($s === '') {
            return response()->json([]);
        }

        $medicines = Medicine::query()
            ->where('is_active', 1)
            ->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('product_code', 'like', "%{$s}%")
                    ->orWhere('barcode', 'like', "%{$s}%")
                    ->orWhere('generic_name', 'like', "%{$s}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'product_code', 'barcode', 'name', 'generic_name']);

        return response()->json($medicines);
    }

    public function batches(Request $request, Medicine $medicine): JsonResponse
    {
        abort_unless($medicine->is_active, 404);

        $selected = (int) $request->query('selected', 0);

        $batches = Batch::query()
            ->where('medicine_id', $medicine->id)
            ->where('status', 'Available')
            ->whereDate('expiry_date', '>=', today())
            ->where(function ($q) use ($selected) {
                $q->where('quantity', '>', 0);

                if ($selected > 0) {
                    $q->orWhereKey($selected);
                }
            })
            ->orderBy('expiry_date')
            ->get(['id', 'batch_no', 'quantity', 'sale_price', 'mrp', 'expiry_date']);

        return response()->json($batches);
    }

    public function store(Request $request): RedirectResponse
    {
        $v = $this->validated($request);

        try {
            $sale = DB::transaction(fn () => $this->saveSale($v, $request));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'The sale could not be saved. No stock was changed.');
        }

        return redirect()
            ->route('sales.show', $sale)
            ->with('success', 'Sale created successfully.');
    }

    public function show(Sale $sale): View
    {
        $sale->load(['customer', 'user', 'items.medicine', 'items.batch']);

        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale): View
    {
        $sale->load(['customer', 'items.medicine', 'items.batch']);

        return view('sales.edit', compact('sale'));
    }

    public function update(Request $request, Sale $sale): RedirectResponse
    {
        $v = $this->validated($request);

        try {
            $updated = DB::transaction(function () use ($v, $request, $sale) {
                $this->restoreStock($sale);

                return $this->saveSale($v, $request, $sale);
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'The sale could not be updated. Stock was not changed.');
        }

        return redirect()
            ->route('sales.show', $updated)
            ->with('success', 'Sale updated successfully.');
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        try {
            DB::transaction(function () use ($sale) {
                $this->restoreStock($sale);
                $sale->delete();
            });
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'The sale could not be deleted.');
        }

        return redirect()
            ->route('sales.index')
            ->with('success', 'Sale deleted and stock restored.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'sale_date' => ['required', 'date', 'before_or_equal:today'],
            'invoice_discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medicine_id' => ['required', 'integer', 'exists:medicines,id'],
            'items.*.batch_id' => ['required', 'integer', 'distinct', 'exists:batches,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    private function saveSale(array $v, Request $request, ?Sale $sale = null): Sale
    {
        $customer = Customer::query()
            ->whereKey($v['customer_id'])
            ->where('deleted', 0)
            ->where('is_active', 1)
            ->first();

        if (!$customer) {
            throw ValidationException::withMessages([
                'customer_id' => 'The selected customer is inactive or unavailable.',
            ]);
        }

        $subtotal = 0.0;
        $lineDiscount = 0.0;
        $prepared = [];

        foreach ($v['items'] as $i => $item) {
            $batch = Batch::with('medicine')
                ->whereKey($item['batch_id'])
                ->lockForUpdate()
                ->first();

            if (!$batch || (int) $batch->medicine_id !== (int) $item['medicine_id']) {
                throw ValidationException::withMessages([
                    "items.$i.batch_id" => 'The selected batch does not belong to the selected medicine.',
                ]);
            }

            if (!$batch->medicine || !$batch->medicine->is_active) {
                throw ValidationException::withMessages([
                    "items.$i.medicine_id" => 'The selected medicine is inactive or unavailable.',
                ]);
            }

            if ($batch->status !== 'Available' || $batch->expiry_date->lt(today())) {
                throw ValidationException::withMessages([
                    "items.$i.batch_id" => "Batch {$batch->batch_no} is expired or unavailable.",
                ]);
            }

            if ((int) $item['quantity'] > (int) $batch->quantity) {
                throw ValidationException::withMessages([
                    "items.$i.quantity" => "Only {$batch->quantity} units are available for batch {$batch->batch_no}.",
                ]);
            }

            if ((float) $item['unit_price'] > (float) $batch->mrp) {
                throw ValidationException::withMessages([
                    "items.$i.unit_price" => 'Sale price cannot be greater than MRP.',
                ]);
            }

            $gross = round((int) $item['quantity'] * (float) $item['unit_price'], 2);
            $discount = round((float) ($item['discount'] ?? 0), 2);

            if ($discount > $gross) {
                throw ValidationException::withMessages([
                    "items.$i.discount" => 'Line discount cannot be greater than the line amount.',
                ]);
            }

            $subtotal += $gross;
            $lineDiscount += $discount;

            $prepared[] = [
                $batch,
                (int) $item['quantity'],
                round((float) $item['unit_price'], 2),
                $discount,
                round($gross - $discount, 2),
            ];
        }

        $invoiceDiscountPercent = round((float) ($v['invoice_discount'] ?? 0), 2);
        $afterLineDiscount = max(0, $subtotal - $lineDiscount);
        $invoiceDiscountAmount = round($afterLineDiscount * ($invoiceDiscountPercent / 100), 2);
        $grand = round($afterLineDiscount - $invoiceDiscountAmount, 2);

        $paid = round((float) $v['paid_amount'], 2);

        if ($paid > $grand) {
            throw ValidationException::withMessages([
                'paid_amount' => 'Paid amount cannot be greater than the grand total.',
            ]);
        }

        $due = round($grand - $paid, 2);

        $existingDue = Sale::query()
            ->where('customer_id', $customer->id)
            ->when($sale, fn ($q) => $q->where('id', '!=', $sale->id))
            ->get()
            ->sum(fn ($oldSale) => max(0, (float) $oldSale->grand_total - (float) $oldSale->paid_amount));

        // A credit limit of 0 is treated as unlimited credit.
        // Otherwise, only the customer's actual outstanding balance is checked.
        $creditLimit = round((float) $customer->credit_limit, 2);
        $projectedOutstanding = round(
            (float) $customer->opening_balance + $existingDue + $due,
            2
        );

        if ($due > 0 && $creditLimit > 0 && $projectedOutstanding > $creditLimit) {
            $remainingCredit = max(0, round($creditLimit - ((float) $customer->opening_balance + $existingDue), 2));

            throw ValidationException::withMessages([
                'paid_amount' => 'This sale exceeds the customer credit limit. Remaining available credit: Rs. ' . number_format($remainingCredit, 2),
            ]);
        }

        $paymentStatus = match (true) {
            $due <= 0 => 'Paid',
            $paid > 0 => 'Partial',
            default => 'Pending',
        };

        $data = [
            'customer_id' => $customer->id,
            'sale_date' => $v['sale_date'],
            'payment_status' => $paymentStatus,
            'subtotal' => round($subtotal, 2),
            'discount' => round($lineDiscount + $invoiceDiscountAmount, 2),
            // This column now stores the percentage entered by the user.
            'invoice_discount' => $invoiceDiscountPercent,
            'tax' => 0,
            'grand_total' => $grand,
            'paid_amount' => $paid,
            'status' => 'Completed',
            'created_by' => $sale?->created_by ?? $request->user()->id,
        ];

        if (!$sale) {
            $data['invoice_no'] = $this->invoice();
            $sale = Sale::create($data);
        } else {
            $sale->items()->delete();
            $sale->update($data);
        }

        foreach ($prepared as [$batch, $quantity, $price, $discount, $total]) {
            $batch->decrement('quantity', $quantity);

            $sale->items()->create([
                'medicine_id' => $batch->medicine_id,
                'batch_id' => $batch->id,
                'quantity' => $quantity,
                'unit_price' => $price,
                'discount' => $discount,
                'tax' => 0,
                'total' => $total,
            ]);
        }

        return $sale->fresh();
    }

    private function restoreStock(Sale $sale): void
    {
        $sale->load('items');

        foreach ($sale->items as $item) {
            $batch = Batch::lockForUpdate()->find($item->batch_id);

            if ($batch) {
                $batch->increment('quantity', $item->quantity);
            }
        }
    }

    private function invoice(): string
    {
        do {
            $number = 'SAL-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Sale::where('invoice_no', $number)->exists());

        return $number;
    }
}
