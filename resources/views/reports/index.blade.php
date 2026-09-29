@extends('layouts.admin')

@section('title', 'Admin Reports')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="mb-0">Reports</h1>
        <small class="text-muted">Sales and collection overview for {{ $from }} to {{ $to }}</small>
    </div>
    <button class="btn btn-outline-secondary" onclick="window.print()"><i class="fas fa-print mr-1"></i> Print</button>
</div>
@stop

@section('content')
<form method="GET" class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-end">
            <div class="col-md-4 mb-2">
                <label>From Date</label>
                <input type="date" name="from_date" value="{{ $from }}" class="form-control">
            </div>
            <div class="col-md-4 mb-2">
                <label>To Date</label>
                <input type="date" name="to_date" value="{{ $to }}" class="form-control">
            </div>
            <div class="col-md-4 mb-2">
                <button class="btn btn-primary mr-1"><i class="fas fa-filter mr-1"></i> Apply Filter</button>
                <a href="{{ route('reports.index') }}" class="btn btn-light">Reset</a>
            </div>
        </div>
    </div>
</form>

<div class="row">
    @php
        $cards = [
            ['label'=>'Invoices','value'=>number_format($summary['invoice_count']), 'icon'=>'fas fa-file-invoice'],
            ['label'=>'Net Sales','value'=>'Rs. '.number_format($summary['net_sales'],2), 'icon'=>'fas fa-chart-line'],
            ['label'=>'Collections','value'=>'Rs. '.number_format($summary['collections'],2), 'icon'=>'fas fa-hand-holding-usd'],
            ['label'=>'Outstanding','value'=>'Rs. '.number_format($summary['outstanding'],2), 'icon'=>'fas fa-wallet'],
        ];
    @endphp
    @foreach($cards as $card)
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 report-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><small class="text-muted text-uppercase font-weight-bold">{{ $card['label'] }}</small><h3 class="mb-0 mt-1 font-weight-bold">{{ $card['value'] }}</h3></div>
                    <div class="report-icon"><i class="{{ $card['icon'] }}"></i></div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0"><strong>Daily Sales</strong></div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Date</th><th>Invoices</th><th>Net Sales</th><th>Paid</th><th>Due</th></tr></thead>
                    <tbody>
                    @forelse($daily as $day)
                        <tr><td>{{ $day['date'] }}</td><td>{{ $day['invoices'] }}</td><td>Rs. {{ number_format($day['net_sales'],2) }}</td><td>Rs. {{ number_format($day['paid'],2) }}</td><td>Rs. {{ number_format($day['due'],2) }}</td></tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No sales found for this period.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0"><strong>Top Products</strong></div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Product</th><th>Qty</th><th>Sales</th></tr></thead>
                    <tbody>
                    @forelse($topProducts as $item)
                        <tr><td><strong>{{ $item->medicine->name ?? 'N/A' }}</strong><br><small class="text-muted">{{ $item->medicine->product_code ?? '' }}</small></td><td>{{ $item->quantity }}</td><td>Rs. {{ number_format((float)$item->sales,2) }}</td></tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">No product sales.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0"><strong>Sales Register</strong></div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-striped mb-0">
            <thead><tr><th>Invoice</th><th>Date</th><th>Customer</th><th>Net Total</th><th>Paid</th><th>Due</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($sales as $sale)
                <tr><td><a href="{{ route('sales.show',$sale) }}">{{ $sale->invoice_no }}</a></td><td>{{ $sale->sale_date->format('Y-m-d') }}</td><td>{{ $sale->customer->business_name ?? 'N/A' }}</td><td>Rs. {{ number_format((float)$sale->grand_total,2) }}</td><td>Rs. {{ number_format((float)$sale->paid_amount,2) }}</td><td>Rs. {{ number_format($sale->due_amount,2) }}</td><td><span class="badge badge-{{ strtolower($sale->payment_status)==='paid'?'success':(strtolower($sale->payment_status)==='partial'?'warning':'danger') }}">{{ $sale->payment_status }}</span></td></tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No sales found for this period.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop

@push('css')
<style>
.report-card { border-radius: 12px; }
.report-icon { width: 46px; height: 46px; border-radius: 12px; display:flex; align-items:center; justify-content:center; background:#f1f5f9; color:#334155; font-size:1.15rem; }
@media print { .main-sidebar, .main-header, .content-header button, form { display:none !important; } .content-wrapper { margin-left:0 !important; } .card { box-shadow:none !important; } }
</style>
@endpush
