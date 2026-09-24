@extends('layouts.admin')
@section('title','Edit Sale')
@section('content_header')<div class="d-flex justify-content-between align-items-center"><h1>Edit Sale {{ $sale->invoice_no }}</h1><a class="btn btn-secondary" href="{{ route('sales.show',$sale) }}">Back</a></div>@stop
@section('content')<div class="alert alert-warning"><strong>Stock:</strong> Updating this sale restores its old quantities and deducts the new quantities in one database transaction.</div>@include('sales._form',['sale'=>$sale])@stop
