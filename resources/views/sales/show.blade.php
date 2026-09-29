@extends('layouts.admin')

@section('title', 'Sale '.$sale->invoice_no)

@section('content_header')
<div class="d-flex justify-content-between align-items-center no-print">

    <h1>Sale {{ $sale->invoice_no }}</h1>

    <div>

        @can('admin')
            <a class="btn btn-warning"
               href="{{ route('sales.edit', $sale) }}">
                Edit
            </a>
        @endcan

        <button type="button"
                class="btn btn-primary"
                onclick="window.print()">
            <i class="fas fa-print mr-1"></i>
            Print Receipt
        </button>

        <a class="btn btn-secondary"
           href="{{ route('sales.index') }}">
            Back
        </a>

    </div>

</div>
@stop


@section('content')

@if(session('success'))
    <div class="alert alert-success no-print">
        {{ session('success') }}
    </div>
@endif


<div id="printArea" class="medical-invoice">

    {{-- =====================================================
         COMPANY HEADER
    ====================================================== --}}

    <div class="invoice-header">

        <div class="company-section">

            <div class="company-name">
                {{ config('app.name') !== 'Laravel'
                    ? config('app.name')
                    : 'WHOLESALE MEDICAL DISTRIBUTOR' }}
            </div>

            <div class="company-address">
                MAIN TOPI ROAD NEAR JHANDA ROAD
            </div>

            <div class="company-address">
                OPP FAZAL MARIABLE FACTORY
            </div>

            <div class="company-phone">
                PHONE: 0300-9082019
            </div>

        </div>


        <div class="invoice-title">
            INVOICE
        </div>

    </div>


    {{-- =====================================================
         CUSTOMER / INVOICE INFORMATION

         NO BORDER HERE
    ====================================================== --}}

    <div class="customer-details">

        <div class="details-left">

            <div class="detail-row">
                <span>INVOICE NO</span>
                <strong>
                    {{ $sale->invoice_no }}
                </strong>
            </div>

            <div class="detail-row">
                <span>PARTY CODE</span>
                <strong>
                    {{ $sale->customer->customer_code ?? '-' }}
                </strong>
            </div>

            <div class="detail-row">
                <span>PARTY NAME</span>
                <strong>
                    {{ $sale->customer->business_name ?? 'Cash Customer' }}
                </strong>
            </div>

            <div class="detail-row">
                <span>ADDRESS</span>
                <strong>
                    {{ $sale->customer->address ?? '-' }}
                </strong>
            </div>

            <div class="detail-row">
                <span>PHONE</span>
                <strong>
                    {{ $sale->customer->phone ?? '-' }}
                </strong>
            </div>

        </div>


        <div class="details-right">

            <div class="detail-row">
                <span>DATE</span>
                <strong>
                    {{ $sale->sale_date?->format('d F Y') }}
                </strong>
            </div>

            <div class="detail-row">
                <span>TIME</span>
                <strong>
                    {{ $sale->created_at?->format('H:i:s') ?? '-' }}
                </strong>
            </div>

            <div class="detail-row">
                <span>SALESMAN</span>
                <strong>
                    {{ $sale->user->name ?? '-' }}
                </strong>
            </div>

            <div class="detail-row">
                <span>PAYMENT</span>
                <strong>
                    {{ strtoupper($sale->payment_status ?? '-') }}
                </strong>
            </div>

        </div>

    </div>


    {{-- =====================================================
         PRODUCT TABLE
         THIS IS THE ONLY MAIN SECTION WITH BORDERS
    ====================================================== --}}

    <table class="invoice-table">

        <thead>

            <tr>

                <th class="code-column">
                    CODE
                </th>

                <th class="product-column">
                    PARTICULAR
                </th>

                <th class="batch-column">
                    BATCH
                </th>

                <th class="expiry-column">
                    EXPIRY
                </th>

                <th class="rate-column">
                    RATE
                </th>

                <th class="qty-column">
                    QTY
                </th>

                <th class="bonus-column">
                    BONUS
                </th>

                <th class="discount-column">
                    DISC %
                </th>

                <th class="total-column">
                    NET TOTAL
                </th>

            </tr>

        </thead>


        <tbody>

        @foreach($sale->items as $item)

            @php

                $gross =
                    (float) $item->quantity *
                    (float) $item->unit_price;

                $discountPercent =
                    (float) ($item->discount_percent ?? 0);

                /*
                 * Old sales compatibility.
                 *
                 * Older records may have discount stored
                 * as a monetary amount.
                 */
                if (
                    $discountPercent <= 0 &&
                    $gross > 0 &&
                    (float) $item->discount > 0
                ) {

                    $discountPercent = round(
                        (
                            (float) $item->discount /
                            $gross
                        ) * 100,
                        2
                    );

                }

            @endphp


            <tr>

                <td>
                    {{ $item->medicine->product_code ?? '-' }}
                </td>

                <td class="product-name">
                    {{ $item->medicine->name ?? 'N/A' }}
                </td>

                <td>
                    {{ $item->batch->batch_no ?? '-' }}
                </td>

                <td>
                    {{ $item->batch?->expiry_date?->format('d/m/Y') ?? '-' }}
                </td>

                <td class="text-right">
                    {{ number_format((float) $item->unit_price, 2) }}
                </td>

                <td class="text-center">
                    {{ $item->quantity }}
                </td>

                <td class="text-center">
                    0
                </td>

                <td class="text-center">
                    {{ number_format($discountPercent, 2) }}
                </td>

                <td class="text-right">
                    {{ number_format((float) $item->total, 2) }}
                </td>

            </tr>

        @endforeach

        </tbody>


        {{-- =================================================
             TOTALS
        ================================================== --}}

        <tfoot>

            <tr>

                <th colspan="7"
                    class="total-description">
                    GRAND TOTAL
                </th>

                <td colspan="2"
                    class="total-value">
                    {{ number_format((float) $sale->subtotal, 2) }}
                </td>

            </tr>


            <tr>

                <th colspan="7"
                    class="total-description">
                    DISCOUNT
                </th>

                <td colspan="2"
                    class="total-value">
                    {{ number_format((float) $sale->discount, 2) }}
                </td>

            </tr>


            <tr>

                <th colspan="7"
                    class="total-description">
                    SALES TAX
                </th>

                <td colspan="2"
                    class="total-value">
                    {{ number_format((float) $sale->tax, 2) }}
                </td>

            </tr>


            <tr class="net-total-row">

                <th colspan="7"
                    class="total-description">
                    NET TOTAL
                </th>

                <th colspan="2"
                    class="total-value">

                    Rs.
                    {{ number_format((float) $sale->grand_total, 2) }}

                </th>

            </tr>

        </tfoot>

    </table>


    {{-- =====================================================
         PAYMENT SUMMARY
    ====================================================== --}}

    <div class="payment-summary">

        <div class="payment-item">

            <span>PAID</span>

            <strong>
                Rs.
                {{ number_format((float) $sale->paid_amount, 2) }}
            </strong>

        </div>


        <div class="payment-item">

            <span>BALANCE</span>

            <strong>
                Rs.
                {{ number_format((float) $sale->due_amount, 2) }}
            </strong>

        </div>


        <div class="payment-item">

            <span>PAYMENT STATUS</span>

            <strong>
                {{ strtoupper($sale->payment_status ?? '-') }}
            </strong>

        </div>

    </div>


    {{-- =====================================================
         WARRANTY / SIGNATURE
    ====================================================== --}}

    <div class="bottom-section">

        <div class="warranty-section">

            <div class="section-title">
                WARRANTY:
            </div>

            <div class="warranty-text">

                Goods once sold will not be returned without
                prior approval.

                Claims, shortages and discrepancies should
                be reported promptly.

                Please mention invoice number and batch
                number for any complaint.

            </div>

        </div>


        <div class="signature-section">

            <div class="signature-line"></div>

            <div class="signature-title">
                AUTHORIZED SIGNATURE
            </div>

            <div class="signature-name">
                {{ $sale->user->name ?? '' }}
            </div>

        </div>

    </div>


    {{-- =====================================================
         FOOTER
    ====================================================== --}}

    <div class="invoice-footer">

        <span>
            Thank you for your business.
        </span>

        <span>
            {{ config('app.name') !== 'Laravel'
                ? config('app.name')
                : 'Medical Distributor' }}
        </span>

    </div>


    {{-- =====================================================
         PAYMENT HISTORY
         SCREEN ONLY
    ====================================================== --}}

    @if($sale->allocations->isNotEmpty())

        <div class="payment-history no-print">

            <strong>
                Payments Allocated To This Sale
            </strong>


            <table class="table table-sm table-bordered mt-2">

                <thead>

                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Reference</th>
                        <th>Recorded By</th>
                    </tr>

                </thead>


                <tbody>

                @foreach($sale->allocations as $allocation)

                    <tr>

                        <td>
                            {{ $allocation->payment->payment_date?->format('Y-m-d') }}
                        </td>

                        <td>
                            Rs.
                            {{ number_format(
                                (float) $allocation->amount,
                                2
                            ) }}
                        </td>

                        <td>
                            {{ $allocation->payment->reference_no ?: '-' }}
                        </td>

                        <td>
                            {{ $allocation->payment->creator->name ?? '-' }}
                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    @endif


</div>

@stop



{{-- =========================================================
     RECEIPT CSS
========================================================= --}}

@push('css')

<style>

/* =========================================================
   MAIN RECEIPT
========================================================= */

.medical-invoice {

    width: 100%;

    max-width: 820px;

    margin: 0 auto;

    padding: 24px 28px;

    background: #ffffff;

    color: #111111;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    font-size: 11px;

    line-height: 1.35;

}


/* =========================================================
   HEADER
========================================================= */

.invoice-header {

    display: flex;

    justify-content: space-between;

    align-items: flex-start;

    padding-bottom: 12px;

}


.company-section {

    flex: 1;

}


.company-name {

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 21px;

    font-weight: 700;

    line-height: 1.15;

    text-transform: uppercase;

    margin-bottom: 4px;

}


.company-address {

    font-size: 9.5px;

    line-height: 1.4;

    text-transform: uppercase;

}


.company-phone {

    font-size: 9.5px;

    font-weight: 700;

    margin-top: 2px;

}


.invoice-title {

    border: 2px solid #111;

    padding: 7px 13px;

    margin-left: 20px;

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 23px;

    font-weight: 700;

    letter-spacing: 1px;

}


/* =========================================================
   CUSTOMER DETAILS
   NO BORDER
========================================================= */

.customer-details {

    display: grid;

    grid-template-columns: 1.25fr 0.9fr;

    column-gap: 35px;

    margin-bottom: 14px;

}


.details-left,
.details-right {

    min-width: 0;

}


.detail-row {

    display: flex;

    align-items: flex-start;

    min-height: 21px;

    line-height: 1.4;

}


.detail-row span {

    width: 92px;

    flex: 0 0 92px;

    font-size: 9px;

    font-weight: 700;

}


.detail-row strong {

    flex: 1;

    font-size: 9.5px;

    font-weight: 500;

    overflow-wrap: anywhere;

}


/* =========================================================
   PRODUCT TABLE
========================================================= */

.invoice-table {

    width: 100%;

    border-collapse: collapse;

    table-layout: fixed;

    margin: 0;

}


/*
 * Borders are ONLY applied to the table.
 */

.invoice-table th,
.invoice-table td {

    border: 1px solid #222;

    padding: 6px 5px;

    vertical-align: middle;

}


/* =========================================================
   TABLE HEADER
========================================================= */

.invoice-table thead th {

    font-size: 9.5px;

    font-weight: 700;

    text-align: center;

    background: #ffffff;

    height: 28px;

}


/* =========================================================
   TABLE BODY
========================================================= */

.invoice-table tbody td {

    font-size: 9.5px;

    height: 27px;

}


.product-name {

    text-align: left;

    font-weight: 500;

    overflow-wrap: anywhere;

}


.text-right {

    text-align: right;

}


.text-center {

    text-align: center;

}


/* =========================================================
   TABLE WIDTHS
========================================================= */

.code-column {

    width: 10%;

}


.product-column {

    width: 23%;

}


.batch-column {

    width: 11%;

}


.expiry-column {

    width: 10%;

}


.rate-column {

    width: 9%;

}


.qty-column {

    width: 7%;

}


.bonus-column {

    width: 7%;

}


.discount-column {

    width: 9%;

}


.total-column {

    width: 14%;

}


/* =========================================================
   TABLE FOOTER
========================================================= */

.invoice-table tfoot th,
.invoice-table tfoot td {

    font-size: 9.5px;

    font-weight: 700;

    padding: 7px 6px;

}


.total-description {

    text-align: right;

    padding-right: 12px !important;

}


.total-value {

    text-align: right;

}


.net-total-row th,
.net-total-row td {

    font-size: 12px !important;

    font-weight: 700;

    padding-top: 8px !important;

    padding-bottom: 8px !important;

}


/* =========================================================
   PAYMENT SUMMARY
========================================================= */

.payment-summary {

    display: flex;

    justify-content: flex-end;

    gap: 30px;

    margin-top: 10px;

    margin-bottom: 15px;

}


.payment-item {

    display: flex;

    gap: 7px;

    align-items: center;

    font-size: 9.5px;

}


.payment-item span {

    font-weight: 700;

}


.payment-item strong {

    font-weight: 600;

}


/* =========================================================
   BOTTOM SECTION
========================================================= */

.bottom-section {

    display: grid;

    grid-template-columns: 1fr 220px;

    column-gap: 35px;

    align-items: end;

    margin-top: 12px;

}


.warranty-section {

    font-size: 8.5px;

    line-height: 1.45;

}


.section-title {

    font-weight: 700;

    font-size: 9px;

    margin-bottom: 3px;

}


.warranty-text {

    max-width: 470px;

}


.signature-section {

    text-align: center;

    font-size: 9px;

}


.signature-line {

    height: 35px;

    border-bottom: 1px solid #111;

    margin-bottom: 5px;

}


.signature-title {

    font-weight: 700;

}


.signature-name {

    margin-top: 2px;

    font-size: 8px;

}


/* =========================================================
   FOOTER
========================================================= */

.invoice-footer {

    display: flex;

    justify-content: space-between;

    border-top: 1px solid #333;

    margin-top: 12px;

    padding-top: 5px;

    font-size: 8px;

}


/* =========================================================
   SCREEN PAYMENT HISTORY
========================================================= */

.payment-history {

    margin-top: 30px;

}


/* =========================================================
   PRINT SETTINGS
========================================================= */

@media print {

    @page {

        size: A4 portrait;

        margin: 9mm 10mm;

    }


    html,
    body {

        width: 100%;

        margin: 0 !important;

        padding: 0 !important;

        background: #ffffff !important;

    }


    body * {

        visibility: hidden;

    }


    #printArea,
    #printArea * {

        visibility: visible;

    }


    #printArea {

        position: absolute;

        left: 0;

        top: 0;

        width: 100%;

        max-width: none;

        margin: 0;

        padding: 0;

        background: #ffffff;

    }


    .no-print {

        display: none !important;

    }


    /*
     * Repeat the product table header
     * automatically on page 2, 3, etc.
     */

    .invoice-table thead {

        display: table-header-group;

    }


    .invoice-table tfoot {

        display: table-row-group;

    }


    /*
     * Prevent an individual product row
     * from splitting between pages.
     */

    .invoice-table tr {

        page-break-inside: avoid;

    }


    .invoice-table td,
    .invoice-table th {

        -webkit-print-color-adjust: exact;

        print-color-adjust: exact;

    }


    .invoice-header {

        page-break-inside: avoid;

    }


    .customer-details {

        page-break-inside: avoid;

    }


    .payment-summary {

        page-break-inside: avoid;

    }


    .bottom-section {

        page-break-inside: avoid;

    }


    .invoice-footer {

        page-break-inside: avoid;

    }

}


/* =========================================================
   SCREEN RESPONSIVE
========================================================= */

@media screen and (max-width: 768px) {

    .medical-invoice {

        padding: 15px;

    }


    .customer-details {

        grid-template-columns: 1fr;

        row-gap: 8px;

    }


    .bottom-section {

        grid-template-columns: 1fr;

        row-gap: 20px;

    }


    .payment-summary {

        justify-content: flex-start;

        flex-wrap: wrap;

    }


    .invoice-table {

        font-size: 8px;

    }

}

</style>

@endpush