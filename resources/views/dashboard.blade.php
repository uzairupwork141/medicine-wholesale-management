@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0 text-dark font-weight-bold">
            <i class="fas fa-tachometer-alt mr-2 text-primary"></i>Dashboard
        </h1>
        <small class="text-muted">
            <i class="far fa-calendar-alt mr-1"></i>
            {{ now()->format('l, d M Y') }}
        </small>
    </div>
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
                        <i class="fas fa-clinic-medical text-white" style="font-size: 4rem; opacity: .25;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ STAT CARDS ============ --}}
    <div class="row">
        @php
            $cards = [
                [
                    'count' => $totalMedicines ?? 0,
                    'label' => 'Medicines',
                    'icon'  => 'fas fa-pills',
                    'color' => '#007bff',
                    'bg'    => 'rgba(0,123,255,.12)',
                ],
                [
                    'count' => $totalCustomers ?? 0,
                    'label' => 'Customers',
                    'icon'  => 'fas fa-users',
                    'color' => '#28a745',
                    'bg'    => 'rgba(40,167,69,.12)',
                ],
                [
                    'count' => $totalSales ?? 0,
                    'label' => 'Sales',
                    'icon'  => 'fas fa-shopping-cart',
                    'color' => '#ffc107',
                    'bg'    => 'rgba(255,193,7,.15)',
                ],
                [
                    'count' => $lowStock ?? 0,
                    'label' => 'Low Stock',
                    'icon'  => 'fas fa-exclamation-triangle',
                    'color' => '#dc3545',
                    'bg'    => 'rgba(220,53,69,.12)',
                ],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3">
                <div class="card border-0 shadow-sm h-100 stat-card"
                     style="border-radius: 14px; transition: all .3s ease;">
                    <div class="card-body d-flex align-items-center justify-content-between py-4">

                        <div>
                            <p class="text-uppercase text-muted mb-1 font-weight-bold"
                               style="font-size: .72rem; letter-spacing: 1px;">
                                {{ $card['label'] }}
                            </p>
                            <h2 class="mb-0 font-weight-bold" style="color: #1a1a2e;">
                                {{ number_format($card['count']) }}
                            </h2>
                        </div>

                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                             style="width: 60px; height: 60px; background: {{ $card['bg'] }};">
                            <i class="{{ $card['icon'] }}" style="font-size: 1.5rem; color: {{ $card['color'] }};"></i>
                        </div>

                    </div>
                    <div style="height: 4px; background: {{ $card['color'] }}; border-radius: 0 0 14px 14px;"></div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ============ QUICK ACTIONS ============ --}}
    <div class="row mt-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 14px;">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h5 class="font-weight-bold mb-0" style="color: #1a1a2e;">
                        <i class="fas fa-bolt text-warning mr-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                            <a href="{{ url('/medicines') }}"
                               class="btn btn-light btn-block py-3 shadow-sm quick-btn"
                               style="border-radius: 12px; border: 1px solid #eaeaea;">
                                <i class="fas fa-plus-circle fa-2x d-block mb-2 text-primary"></i>
                                <span class="font-weight-bold text-dark">Add Medicine</span>
                            </a>
                        </div>

                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                            <a href="{{ url('/customers') }}"
                               class="btn btn-light btn-block py-3 shadow-sm quick-btn"
                               style="border-radius: 12px; border: 1px solid #eaeaea;">
                                <i class="fas fa-user-plus fa-2x d-block mb-2 text-success"></i>
                                <span class="font-weight-bold text-dark">Add Customer</span>
                            </a>
                        </div>

                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                            <a href="{{ url('/sales') }}"
                               class="btn btn-light btn-block py-3 shadow-sm quick-btn"
                               style="border-radius: 12px; border: 1px solid #eaeaea;">
                                <i class="fas fa-cash-register fa-2x d-block mb-2 text-warning"></i>
                                <span class="font-weight-bold text-dark">New Sale</span>
                            </a>
                        </div>

                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                            <a href="{{ url('/reports') }}"
                               class="btn btn-light btn-block py-3 shadow-sm quick-btn"
                               style="border-radius: 12px; border: 1px solid #eaeaea;">
                                <i class="fas fa-chart-bar fa-2x d-block mb-2 text-danger"></i>
                                <span class="font-weight-bold text-dark">Reports</span>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@push('styles')
<style>
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, .08) !important;
    }
    .quick-btn:hover {
        background: #f8f9fa !important;
        transform: translateY(-2px);
        transition: all .2s ease;
    }
</style>
@endpush