@extends('layouts.admin')
@section('title','Customer Pending Payments')
@section('content_header')<h1>Customer Pending Payments</h1>@stop
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<div class="card"><div class="card-body table-responsive"><table class="table table-bordered"><thead><tr><th>Customer</th><th>Phone</th><th>Pending Sales</th><th>Total Outstanding</th><th>Collect Payment</th></tr></thead><tbody>
@forelse($customers as $customer)<tr><td><strong>{{ $customer->business_name }}</strong><br><small>{{ $customer->customer_code }}</small></td><td>{{ $customer->phone }}</td><td>{{ $customer->sales->count() }}</td><td>Rs. {{ number_format($customer->pending_total,2) }}</td><td><form method="POST" action="{{ route('sales.payments.store') }}" class="form-row">@csrf<input type="hidden" name="customer_id" value="{{ $customer->id }}"><div class="col-md-4"><input class="form-control" type="number" step="0.01" min="0.01" max="{{ $customer->pending_total }}" name="amount" placeholder="Amount" required></div><div class="col-md-3"><input class="form-control" type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}" required></div><div class="col-md-3"><input class="form-control" name="reference_no" maxlength="100" placeholder="Receipt/reference"></div><div class="col-md-2"><button class="btn btn-success">Collect</button></div></form></td></tr><tr class="table-light"><td colspan="5"><small><strong>Oldest pending sales:</strong> @foreach($customer->sales->take(5) as $sale){{ $sale->invoice_no }} (Due Rs. {{ number_format($sale->due_amount,2) }})@if(!$loop->last), @endif @endforeach @if($customer->sales->count()>5) … @endif</small></td></tr>@empty<tr><td colspan="5" class="text-center">No customers have pending sales.</td></tr>@endforelse
</tbody></table></div></div>
@stop
