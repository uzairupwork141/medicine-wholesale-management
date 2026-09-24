@extends('layouts.admin')
@section('title','New Sale')
@section('content_header')<div class="d-flex justify-content-between align-items-center"><h1>New Sale</h1><a class="btn btn-secondary" href="{{ route('sales.index') }}">Back</a></div>@stop
@section('content')@include('sales._form',['sale'=>null])@stop
