<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Medicine;
use App\Models\PaymentAllocation;
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
        $query = Sale::with(['customer','user'])->latest('sale_date')->latest('id');

        if ($request->filled('search')) {
            $s = trim($request->string('search')->toString());
            $query->where(function ($q) use ($s) {
                $q->where('invoice_no','like',"%{$s}%")
                  ->orWhereHas('customer', fn($c) => $c->where('business_name','like',"%{$s}%")
                      ->orWhere('customer_code','like',"%{$s}%")
                      ->orWhere('phone','like',"%{$s}%"));
            });
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->string('payment_status')->toString());
        }
        if ($request->filled('from_date')) $query->whereDate('sale_date','>=',$request->date('from_date'));
        if ($request->filled('to_date')) $query->whereDate('sale_date','<=',$request->date('to_date'));

        $sales = $query->paginate(20)->withQueryString();
        return view('sales.index', compact('sales'));
    }

    public function create(): View { return view('sales.create'); }

    public function searchCustomers(Request $request): JsonResponse
    {
        $s = trim($request->string('q')->toString());
        if ($s === '') return response()->json([]);
        $customers = Customer::query()->where('deleted',false)->where('is_active',true)
            ->where(fn($q) => $q->where('business_name','like',"%{$s}%")->orWhere('customer_code','like',"%{$s}%")->orWhere('phone','like',"%{$s}%"))
            ->orderBy('business_name')->limit(20)->get(['id','customer_code','business_name','phone']);
        return response()->json($customers);
    }

    public function searchMedicines(Request $request): JsonResponse
    {
        $s = trim($request->string('q')->toString());
        if ($s === '') return response()->json([]);
        $medicines = Medicine::query()->where('is_active',true)
            ->where(fn($q) => $q->where('name','like',"%{$s}%")->orWhere('product_code','like',"%{$s}%")->orWhere('barcode','like',"%{$s}%")->orWhere('generic_name','like',"%{$s}%"))
            ->orderBy('name')->limit(30)->get(['id','product_code','barcode','name','generic_name']);
        return response()->json($medicines);
    }

    public function batches(Request $request, Medicine $medicine): JsonResponse
    {
        abort_unless($medicine->is_active,404);
        $selected = (int)$request->query('selected',0);
        $batches = Batch::query()->where('medicine_id',$medicine->id)->where('status','Available')->whereDate('expiry_date','>=',today())
            ->where(fn($q) => $q->where('quantity','>',0)->when($selected>0, fn($x)=>$x->orWhereKey($selected)))
            ->orderBy('expiry_date')->get(['id','batch_no','quantity','sale_price','mrp','expiry_date']);
        return response()->json($batches);
    }

    public function store(Request $request): RedirectResponse
    {
        $v = $this->validated($request);
        try {
            $sale = DB::transaction(fn()=> $this->saveSale($v,$request));
        } catch (ValidationException $e) { throw $e; }
        catch (Throwable $e) { report($e); return back()->withInput()->with('error','The sale could not be saved. No stock was changed.'); }
        return redirect()->route('sales.show',$sale)->with('success','Sale created successfully.');
    }

    public function show(Sale $sale): View
    {
        $sale->load(['customer','user','items.medicine','items.batch','allocations.payment']);
        return view('sales.show',compact('sale'));
    }

    public function edit(Sale $sale): View
    {
        $sale->load(['customer','items.medicine','items.batch']);
        return view('sales.edit',compact('sale'));
    }

    public function update(Request $request, Sale $sale): RedirectResponse
    {
        $v = $this->validated($request);
        try {
            $updated = DB::transaction(function() use($v,$request,$sale){
                if ($sale->allocations()->exists()) {
                    throw ValidationException::withMessages(['sale'=>'A sale with recorded customer payments cannot be edited. Use a payment correction process instead.']);
                }
                $this->restoreStock($sale);
                return $this->saveSale($v,$request,$sale);
            });
        } catch (ValidationException $e) { throw $e; }
        catch (Throwable $e) { report($e); return back()->withInput()->with('error','The sale could not be updated. Stock was not changed.'); }
        return redirect()->route('sales.show',$updated)->with('success','Sale updated successfully.');
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        try {
            DB::transaction(function() use($sale){
                if ($sale->allocations()->exists()) throw ValidationException::withMessages(['sale'=>'A sale with recorded payments cannot be deleted.']);
                $this->restoreStock($sale); $sale->delete();
            });
        } catch (ValidationException $e) { throw $e; }
        catch (Throwable $e) { report($e); return back()->with('error','The sale could not be deleted.'); }
        return redirect()->route('sales.index')->with('success','Sale deleted and stock restored.');
    }

    public function customerBalances(): View
    {
        $customers = Customer::where('deleted',false)->whereHas('sales',fn($q)=>$q->whereColumn('paid_amount','<','grand_total'))
            ->with(['sales'=>fn($q)=>$q->whereColumn('paid_amount','<','grand_total')->orderBy('sale_date')->orderBy('id')])
            ->orderBy('business_name')->get();
        $customers->each(function($c){ $c->pending_total = $c->sales->sum(fn($s)=>max(0,(float)$s->grand_total-(float)$s->paid_amount)); });
        return view('sales.customer-balances',compact('customers'));
    }

    public function collectPayment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id'=>['required','integer','exists:customers,id'],
            'amount'=>['required','numeric','gt:0'],
            'payment_date'=>['required','date','before_or_equal:today'],
            'reference_no'=>['nullable','string','max:100'],
        ]);

        try {
            DB::transaction(function() use($data,$request){
                $customer = Customer::whereKey($data['customer_id'])->where('deleted',false)->where('is_active',true)->lockForUpdate()->first();
                if (!$customer) throw ValidationException::withMessages(['customer_id'=>'Customer is inactive or unavailable.']);

                $remaining = round((float)$data['amount'],2);
                $sales = Sale::where('customer_id',$customer->id)->whereColumn('paid_amount','<','grand_total')
                    ->whereIn('payment_status',['Pending','Partial'])->orderBy('sale_date')->orderBy('id')->lockForUpdate()->get();

                $outstanding = $sales->sum(fn($s)=>max(0,(float)$s->grand_total-(float)$s->paid_amount));
                if ($remaining > round($outstanding,2)) {
                    throw ValidationException::withMessages(['amount'=>'Payment cannot exceed the customer outstanding balance of Rs. '.number_format($outstanding,2).'.']);
                }

                $payment = CustomerPayment::create([
                    'customer_id'=>$customer->id,
                    'payment_date'=>$data['payment_date'],
                    'amount'=>$remaining,
                    'reference_no'=>$data['reference_no'] ?? null,
                    'created_by'=>$request->user()->id,
                ]);

                foreach ($sales as $sale) {
                    if ($remaining <= 0) break;
                    $due = max(0,round((float)$sale->grand_total-(float)$sale->paid_amount,2));
                    $apply = min($remaining,$due);
                    if ($apply <= 0) continue;
                    $sale->paid_amount = round((float)$sale->paid_amount+$apply,2);
                    $sale->payment_status = ((float)$sale->paid_amount >= (float)$sale->grand_total) ? 'Paid' : 'Partial';
                    $sale->save();
                    PaymentAllocation::create(['customer_payment_id'=>$payment->id,'sale_id'=>$sale->id,'amount'=>$apply]);
                    $remaining = round($remaining-$apply,2);
                }
            });
        } catch (ValidationException $e) { throw $e; }
        catch (Throwable $e) { report($e); return back()->withInput()->with('error','Payment could not be recorded. No payment allocation was changed.'); }

        return back()->with('success','Payment recorded and automatically allocated to the oldest pending sales.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'customer_id'=>['required','integer','exists:customers,id'],
            'sale_date'=>['required','date','before_or_equal:today'],
            'invoice_discount'=>['nullable','numeric','min:0','max:100'],
            'paid_amount'=>['required','numeric','min:0'],
            'items'=>['required','array','min:1'],
            'items.*.medicine_id'=>['required','integer','exists:medicines,id'],
            'items.*.batch_id'=>['required','integer','distinct','exists:batches,id'],
            'items.*.quantity'=>['required','integer','min:1'],
            'items.*.unit_price'=>['required','numeric','min:0'],
            'items.*.discount_percent'=>['nullable','numeric','min:0','max:100'],
        ]);
    }

    private function saveSale(array $v, Request $request, ?Sale $sale=null): Sale
    {
        $customer = Customer::whereKey($v['customer_id'])->where('deleted',false)->where('is_active',true)->first();
        if (!$customer) throw ValidationException::withMessages(['customer_id'=>'The selected customer is inactive or unavailable.']);

        $subtotal=0.0; $lineDiscount=0.0; $prepared=[];
        foreach($v['items'] as $i=>$item){
            $batch=Batch::with('medicine')->whereKey($item['batch_id'])->lockForUpdate()->first();
            if(!$batch || (int)$batch->medicine_id !== (int)$item['medicine_id']) throw ValidationException::withMessages(["items.$i.batch_id"=>'The selected batch does not belong to the selected medicine.']);
            if(!$batch->medicine || !$batch->medicine->is_active) throw ValidationException::withMessages(["items.$i.medicine_id"=>'The selected medicine is inactive or unavailable.']);
            if($batch->status!=='Available' || $batch->expiry_date->lt(today())) throw ValidationException::withMessages(["items.$i.batch_id"=>"Batch {$batch->batch_no} is expired or unavailable."]);
            if((int)$item['quantity']>(int)$batch->quantity) throw ValidationException::withMessages(["items.$i.quantity"=>"Only {$batch->quantity} units are available for batch {$batch->batch_no}."]);
            if((float)$item['unit_price'] > (float)$batch->mrp) throw ValidationException::withMessages(["items.$i.unit_price"=>'Sale price cannot be greater than the batch MRP.']);
            $gross=round((int)$item['quantity']*(float)$item['unit_price'],2);
            $discountPct=round((float)($item['discount_percent']??0),2);
            $discount=round($gross*($discountPct/100),2);
            $subtotal += $gross; $lineDiscount += $discount;
            $prepared[] = [$batch,(int)$item['quantity'],round((float)$item['unit_price'],2),$discountPct,$discount,round($gross-$discount,2)];
        }

        $invoicePct=round((float)($v['invoice_discount']??0),2);
        $afterLine=max(0,$subtotal-$lineDiscount);
        $invoiceAmount=round($afterLine*($invoicePct/100),2);
        $grand=round($afterLine-$invoiceAmount,2);
        $paid=round((float)$v['paid_amount'],2);
        if($paid>$grand) throw ValidationException::withMessages(['paid_amount'=>'Paid amount cannot be greater than the grand total.']);
        $due=round($grand-$paid,2);
        $status=$due<=0?'Paid':($paid>0?'Partial':'Pending');

        $data=[
            'customer_id'=>$customer->id,'sale_date'=>$v['sale_date'],'payment_status'=>$status,
            'subtotal'=>round($subtotal,2),'discount'=>round($lineDiscount+$invoiceAmount,2),'invoice_discount'=>$invoicePct,
            'tax'=>0,'grand_total'=>$grand,'paid_amount'=>$paid,'status'=>'Completed','created_by'=>$sale?->created_by ?? $request->user()->id,
        ];
        if(!$sale){$data['invoice_no']=$this->invoice();$sale=Sale::create($data);} else {$sale->items()->delete();$sale->update($data);}
        foreach($prepared as [$batch,$qty,$price,$discountPct,$discount,$total]){
            $batch->decrement('quantity',$qty);
            $sale->items()->create(['medicine_id'=>$batch->medicine_id,'batch_id'=>$batch->id,'quantity'=>$qty,'unit_price'=>$price,'discount'=>$discount,'discount_percent'=>$discountPct,'tax'=>0,'total'=>$total]);
        }
        return $sale->fresh();
    }

    private function restoreStock(Sale $sale): void
    {
        $sale->load('items');
        foreach($sale->items as $item){$batch=Batch::lockForUpdate()->find($item->batch_id);if($batch)$batch->increment('quantity',$item->quantity);}
    }

    private function invoice(): string
    {
        do {$number='SAL-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));} while(Sale::where('invoice_no',$number)->exists());
        return $number;
    }
}
