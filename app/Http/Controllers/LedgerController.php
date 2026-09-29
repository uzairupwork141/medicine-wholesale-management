<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LedgerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = Customer::query()->where('deleted', false)->orderBy('business_name')->get(['id', 'customer_code', 'business_name', 'phone', 'opening_balance']);
        $customer = null;
        $entries = collect();
        $from = $request->input('from_date', now()->startOfMonth()->toDateString());
        $to = $request->input('to_date', now()->toDateString());
        $opening = 0.0;
        $closing = 0.0;

        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);

        if (!empty($validated['customer_id'])) {
            $customer = $customers->firstWhere('id', (int) $validated['customer_id']);

            if ($customer) {
                $fromDate = $validated['from_date'] ?? $from;
                $toDate = $validated['to_date'] ?? $to;

                $priorSales = Sale::where('customer_id', $customer->id)->whereDate('sale_date', '<', $fromDate)->sum('grand_total');
                $priorPayments = CustomerPayment::where('customer_id', $customer->id)->whereDate('payment_date', '<', $fromDate)->sum('amount');
                $opening = round((float) $customer->opening_balance + (float) $priorSales - (float) $priorPayments, 2);

                $sales = Sale::where('customer_id', $customer->id)->whereBetween('sale_date', [$fromDate, $toDate])->get();
                $payments = CustomerPayment::where('customer_id', $customer->id)->whereBetween('payment_date', [$fromDate, $toDate])->get();

                foreach ($sales as $sale) {
                    $entries->push([
                        'date' => $sale->sale_date,
                        'type' => 'Sale',
                        'reference' => $sale->invoice_no,
                        'description' => 'Credit sale',
                        'debit' => (float) $sale->grand_total,
                        'credit' => 0.0,
                    ]);
                }

                foreach ($payments as $payment) {
                    $entries->push([
                        'date' => $payment->payment_date,
                        'type' => 'Payment',
                        'reference' => $payment->reference_no ?: 'PAY-'.$payment->id,
                        'description' => 'Customer payment',
                        'debit' => 0.0,
                        'credit' => (float) $payment->amount,
                    ]);
                }

                $entries = $entries->sortBy(fn ($entry) => [$entry['date']->format('Y-m-d'), $entry['type'] === 'Sale' ? 0 : 1, $entry['reference']])->values();
                $running = $opening;
                $entries = $entries->map(function ($entry) use (&$running) {
                    $running = round($running + $entry['debit'] - $entry['credit'], 2);
                    $entry['balance'] = $running;
                    return $entry;
                });
                $closing = $running;
            }
        }

        return view('ledger.index', compact('customers', 'customer', 'entries', 'from', 'to', 'opening', 'closing'));
    }
}
