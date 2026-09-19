@extends('adminlte::page')

@section('title', 'Add Customer')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <h1>Add Customer</h1>

        <a href="/customers" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back
        </a>

    </div>

@stop


@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">
            Customer Information
        </h3>
    </div>

    <form action="/customers" method="POST">

        @csrf

        <div class="card-body">

            {{-- Customer Code --}}

            <div class="form-group">
                <label>Customer Code</label>

                <input
                    type="text"
                    name="customer_code"
                    class="form-control"
                    placeholder="CUS-001"
                    value="{{ old('customer_code') }}"
                >

                @error('customer_code')
                    <span class="text-danger">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- Business Name --}}

            <div class="form-group">
                <label>Business Name</label>

                <input
                    type="text"
                    name="business_name"
                    class="form-control"
                    placeholder="ABC Medical Store"
                    value="{{ old('business_name') }}"
                >

                @error('business_name')
                    <span class="text-danger">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- Contact Person --}}

            <div class="form-group">
                <label>Contact Person</label>

                <input
                    type="text"
                    name="contact_person"
                    class="form-control"
                    placeholder="Ahmad Khan"
                    value="{{ old('contact_person') }}"
                >

                @error('contact_person')
                    <span class="text-danger">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- Phone --}}

            <div class="form-group">
                <label>Phone</label>

                <input
                    type="text"
                    name="phone"
                    class="form-control"
                    placeholder="03001234567"
                    value="{{ old('phone') }}"
                >

                @error('phone')
                    <span class="text-danger">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- Email --}}

            <div class="form-group">
                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="example@gmail.com"
                    value="{{ old('email') }}"
                >

                @error('email')
                    <span class="text-danger">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- Address --}}

            <div class="form-group">
                <label>Address</label>

                <textarea
                    name="address"
                    class="form-control"
                    rows="3"
                    placeholder="Customer address"
                >{{ old('address') }}</textarea>

                @error('address')
                    <span class="text-danger">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- License Number --}}

            <div class="form-group">
                <label>License Number</label>

                <input
                    type="text"
                    name="license_no"
                    class="form-control"
                    placeholder="LIC-001"
                    value="{{ old('license_no') }}"
                >

                @error('license_no')
                    <span class="text-danger">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- Credit Limit --}}

            <div class="form-group">
                <label>Credit Limit</label>

                <input
                    type="number"
                    step="0.01"
                    name="credit_limit"
                    class="form-control"
                    placeholder="50000"
                    value="{{ old('credit_limit', 0) }}"
                >

                @error('credit_limit')
                    <span class="text-danger">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- Opening Balance --}}

            <div class="form-group">
                <label>Opening Balance</label>

                <input
                    type="number"
                    step="0.01"
                    name="opening_balance"
                    class="form-control"
                    placeholder="0"
                    value="{{ old('opening_balance', 0) }}"
                >

                @error('opening_balance')
                    <span class="text-danger">
                        {{ $message }}
                    </span>
                @enderror

            </div>


            {{-- Active --}}

            <div class="form-group">

                <div class="custom-control custom-switch">

                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        class="custom-control-input"
                        id="is_active"
                        {{ old('is_active', true) ? 'checked' : '' }}
                    >

                    <label
                        class="custom-control-label"
                        for="is_active"
                    >
                        Active Customer
                    </label>

                </div>

            </div>

        </div>


        <div class="card-footer">

            <button type="submit" class="btn btn-primary">

                <i class="fas fa-save"></i>
                Save Customer

            </button>

            <a href="/customers" class="btn btn-secondary">

                Cancel

            </a>

        </div>

    </form>

</div>

@stop