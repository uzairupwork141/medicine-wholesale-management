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
                 style="background: linear-gradient(135deg, #71a5d2 0%, #2a5298 100%);">
                <div class="card-body d-flex justify-content-between align-items-center py-4">
                    <div>
                        <h4 class="text-white mb-1 font-weight-bold">
                            Welcome back, {{ auth()->user()->name ?? 'Admin' }} 
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

   

@stop

