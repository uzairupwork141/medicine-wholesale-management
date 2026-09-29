@extends('layouts.admin')
@section('title','Sales')
@section('content_header')
<div class="d-flex justify-content-between align-items-center"><h1>Sales</h1><div>@can('admin')<a href="{{ route('sales.customer-balances') }}" class="btn btn-outline-danger mr-2"><i class="bi bi-wallet2"></i> Customer Pending Payments</a>@endcan<a href="{{ route('sales.create') }}" class="btn btn-primary"><i class="bi bi-cart-plus"></i> New Sale</a></div></div>
@stop
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
<div class="card"><div class="card-header"><form class="row" method="GET">
<div class="col-md-4 mb-2"><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Invoice, customer, code or phone"></div>
<div class="col-md-2 mb-2"><select class="form-control" name="payment_status"><option value="">All Payment Status</option>@foreach(['Paid','Partial','Pending'] as $status)<option value="{{ $status }}" @selected(request('payment_status')===$status)>{{ $status }}</option>@endforeach</select></div>
<div class="col-md-2 mb-2"><input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}"></div>
<div class="col-md-2 mb-2"><input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}"></div>
<div class="col-md-2 mb-2"><button class="btn btn-primary">Filter</button> <a class="btn btn-secondary" href="{{ route('sales.index') }}">Reset</a></div>
</form></div>
<div class="card-body table-responsive"><table class="table table-bordered table-hover"><thead><tr><th>Invoice</th><th>Date</th><th>Customer</th><th>Total</th><th>Paid</th><th>Due</th><th>Payment Status</th><th>Created By</th><th>Action</th></tr></thead><tbody>
@forelse($sales as $sale)<tr><td>{{ $sale->invoice_no }}</td><td>{{ $sale->sale_date?->format('Y-m-d') }}</td><td>{{ $sale->customer->business_name ?? 'N/A' }}</td><td>Rs. {{ number_format((float)$sale->grand_total,2) }}</td><td>Rs. {{ number_format((float)$sale->paid_amount,2) }}</td><td>Rs. {{ number_format($sale->due_amount,2) }}</td><td><span class="badge badge-{{ strtolower($sale->payment_status)==='paid'?'success':(strtolower($sale->payment_status)==='partial'?'warning':'danger') }}">{{ $sale->payment_status }}</span></td><td>{{ $sale->user->name ?? 'N/A' }}</td><td><a class="btn btn-sm btn-info" href="{{ route('sales.show',$sale) }}">View</a> @can('admin')<a class="btn btn-sm btn-warning" href="{{ route('sales.edit',$sale) }}">Edit</a><form class="d-inline" method="POST" action="{{ route('sales.destroy',$sale) }}" onsubmit="return confirm('Delete this sale and restore its stock?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Delete</button></form>@endcan</td></tr>@empty<tr><td colspan="9" class="text-center">No sales found.</td></tr>@endforelse
</tbody></table>{{ $sales->links() }}</div></div>
@stop
