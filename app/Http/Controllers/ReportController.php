<?php

namespace App\Http\Controllers;

use App\Models\CustomerPayment;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        [$from, $to] = $this->dates($request);

        $salesQuery = Sale::query()->whereBetween('sale_date', [$from, $to]);
        $sales = (clone $salesQuery)->with(['customer', 'user'])->latest('sale_date')->latest('id')->get();
        $payments = CustomerPayment::query()->whereBetween('payment_date', [$from, $to])->sum('amount');

        $summary = [
            'invoice_count' => $sales->count(),
            'gross_sales' => $sales->sum('subtotal'),
            'discounts' => $sales->sum('discount'),
            'net_sales' => $sales->sum('grand_total'),
            'paid' => $sales->sum('paid_amount'),
            'outstanding' => $sales->sum(fn ($sale) => $sale->due_amount),
            'collections' => (float) $payments,
        ];

        $daily = $sales->groupBy(fn ($sale) => $sale->sale_date->format('Y-m-d'))
            ->map(fn ($rows, $date) => [
                'date' => $date,
                'invoices' => $rows->count(),
                'net_sales' => (float) $rows->sum('grand_total'),
                'paid' => (float) $rows->sum('paid_amount'),
                'due' => (float) $rows->sum(fn ($sale) => $sale->due_amount),
            ])->values()->sortByDesc('date')->values();

        $topProducts = SaleItem::query()
            ->selectRaw('medicine_id, SUM(quantity) as quantity, SUM(total) as sales')
            ->whereHas('sale', fn ($q) => $q->whereBetween('sale_date', [$from, $to]))
            ->with('medicine:id,name,product_code')
            ->groupBy('medicine_id')
            ->orderByDesc('sales')
            ->limit(10)
            ->get();

        return view('reports.index', compact('from', 'to', 'summary', 'daily', 'topProducts', 'sales'));
    }

    private function dates(Request $request): array
    {
        $from = $request->input('from_date', now()->startOfMonth()->toDateString());
        $to = $request->input('to_date', now()->toDateString());

        $validated = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);

        return [$validated['from_date'] ?? $from, $validated['to_date'] ?? $to];
    }
}
