@extends('layouts.admin')

@section('title', 'Customer Ledger')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div><h1 class="mb-0">Customer Ledger</h1><small class="text-muted">Account statement with sales, payments and running balance</small></div>
    @if($customer)<button class="btn btn-outline-secondary" onclick="window.print()"><i class="fas fa-print mr-1"></i> Print Ledger</button>@endif
</div>
@stop

@section('content')
<form method="GET" class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-end">
            <div class="col-lg-5 mb-2">
                <label>Customer</label>
                <select name="customer_id" class="form-control" required>
                    <option value="">Select customer</option>
                    @foreach($customers as $item)
                        <option value="{{ $item->id }}" @selected(request('customer_id') == $item->id)>{{ $item->business_name }} — {{ $item->customer_code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-6 mb-2"><label>From</label><input type="date" name="from_date" value="{{ $from }}" class="form-control"></div>
            <div class="col-lg-2 col-md-6 mb-2"><label>To</label><input type="date" name="to_date" value="{{ $to }}" class="form-control"></div>
            <div class="col-lg-3 mb-2"><button class="btn btn-primary mr-1"><i class="fas fa-search mr-1"></i> View Ledger</button><a href="{{ route('ledger.index') }}" class="btn btn-light">Reset</a></div>
        </div>
    </div>
</form>

@if($customer)
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap">
            <div><h4 class="mb-1">{{ $customer->business_name }}</h4><div class="text-muted">{{ $customer->customer_code }} · {{ $customer->phone }}</div></div>
            <div class="text-right mt-2 mt-md-0"><small class="text-muted d-block">Statement Period</small><strong>{{ $from }} → {{ $to }}</strong></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted text-uppercase font-weight-bold">Opening Balance</small><h4 class="mb-0">Rs. {{ number_format($opening,2) }}</h4></div></div></div>
    <div class="col-md-4 mb-3"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted text-uppercase font-weight-bold">Transactions</small><h4 class="mb-0">{{ $entries->count() }}</h4></div></div></div>
    <div class="col-md-4 mb-3"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted text-uppercase font-weight-bold">Closing Balance</small><h4 class="mb-0">Rs. {{ number_format($closing,2) }}</h4></div></div></div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0 table-responsive">
        <table class="table table-bordered ledger-table mb-0">
            <thead><tr><th>Date</th><th>Type</th><th>Reference</th><th>Description</th><th class="text-right">Debit</th><th class="text-right">Credit</th><th class="text-right">Balance</th></tr></thead>
            <tbody>
                <tr class="table-light"><td>{{ $from }}</td><td colspan="5"><strong>Opening Balance</strong></td><td class="text-right"><strong>Rs. {{ number_format($opening,2) }}</strong></td></tr>
                @forelse($entries as $entry)
                    <tr>
                        <td>{{ $entry['date']->format('Y-m-d') }}</td>
                        <td><span class="badge badge-{{ $entry['type']==='Sale'?'primary':'success' }}">{{ $entry['type'] }}</span></td>
                        <td>{{ $entry['reference'] }}</td>
                        <td>{{ $entry['description'] }}</td>
                        <td class="text-right">{{ $entry['debit'] ? 'Rs. '.number_format($entry['debit'],2) : '—' }}</td>
                        <td class="text-right">{{ $entry['credit'] ? 'Rs. '.number_format($entry['credit'],2) : '—' }}</td>
                        <td class="text-right font-weight-bold">Rs. {{ number_format($entry['balance'],2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No transactions in this period.</td></tr>
                @endforelse
            </tbody>
            <tfoot><tr><th colspan="6" class="text-right">Closing Balance</th><th class="text-right">Rs. {{ number_format($closing,2) }}</th></tr></tfoot>
        </table>
    </div>
</div>
@else
<div class="card border-0 shadow-sm"><div class="card-body text-center py-5"><i class="fas fa-journal-whills fa-3x text-muted mb-3"></i><h4>Select a customer to view the ledger</h4><p class="text-muted mb-0">The ledger calculates opening balance, sales, customer payments and running outstanding balance.</p></div></div>
@endif
@stop

@push('css')
<style>
.ledger-table thead th { white-space:nowrap; background:#f8fafc; }
@media print { .main-sidebar, .main-header, form, .content-header button { display:none !important; } .content-wrapper { margin-left:0 !important; } .card { box-shadow:none !important; } }
</style>
@endpush
