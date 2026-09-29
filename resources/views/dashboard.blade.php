@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content_header') <div class="d-flex justify-content-between align-items-center"> <h1 class="m-0 text-dark font-weight-bold"> <i class="fas fa-tachometer-alt mr-2 text-primary"></i>Dashboard </h1> <small class="text-muted"> <i class="far fa-calendar-alt mr-1"></i>
{{ now()->format('l, d M Y') }} </small> </div>
@stop

@section('content')

{{-- ============ WELCOME BANNER ============ --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm"
             style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
            <div class="card-body d-flex justify-content-between align-items-center py-4">

                <div>
                    <h4 class="text-white mb-1 font-weight-bold">
                        Welcome back, {{ auth()->user()->name ?? 'Admin' }} 👋
                    </h4>

                    <p class="text-white-50 mb-0 small">
                        Here's what's happening with your pharmacy today.
                    </p>
                </div>

                <div class="d-none d-md-block">
                    <i class="fas fa-clinic-medical text-white"
                       style="font-size: 4rem; opacity: .25;"></i>
                </div>

            </div>
        </div>
    </div>
</div>

@stop
