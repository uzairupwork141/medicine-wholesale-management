@extends('adminlte::page')

@section('title', 'Edit Customer')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Edit Customer</h1>

        <a href="/customers" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Back
        </a>
    </div>
@stop

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">Edit Customer Information</h3>
    </div>

    <form action="/customers/{{ $customer->id }}" method="POST">

        @csrf
        @method('PUT')

        <div class="card-body">

            <div class="form-group">
                <label>Customer Code</label>

                <input
                    type="text"
                    name="customer_code"
                    class="form-control"
                    value="{{ old('customer_code', $customer->customer_code) }}"
                >

                @error('customer_code')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>


            <div class="form-group">
                <label>Business Name</label>

                <input
                    type="text"
                    name="business_name"
                    class="form-control"
                    value="{{ old('business_name', $customer->business_name) }}"
                >

                @error('business_name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>


            <div class="form-group">
                <label>Contact Person</label>

                <input
                    type="text"
                    name="contact_person"
                    class="form-control"
                    value="{{ old('contact_person', $customer->contact_person) }}"
                >

                @error('contact_person')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>


            <div class="form-group">
                <label>Phone</label>

                <input
                    type="text"
                    name="phone"
                    class="form-control"
                    value="{{ old('phone', $customer->phone) }}"
                >

                @error('phone')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>


            <div class="form-group">
                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    value="{{ old('email', $customer->email) }}"
                >

                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>


            <div class="form-group">
                <label>Address</label>

                <textarea
                    name="address"
                    class="form-control"
                    rows="3"
                >{{ old('address', $customer->address) }}</textarea>

                @error('address')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>


            <div class="form-group">
                <label>License Number</label>

                <input
                    type="text"
                    name="license_no"
                    class="form-control"
                    value="{{ old('license_no', $customer->license_no) }}"
                >

                @error('license_no')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>


            <div class="form-group">
                <label>Credit Limit</label>

                <input
                    type="number"
                    step="0.01"
                    name="credit_limit"
                    class="form-control"
                    value="{{ old('credit_limit', $customer->credit_limit) }}"
                >

                @error('credit_limit')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>


            <div class="form-group">
                <label>Opening Balance</label>

                <input
                    type="number"
                    step="0.01"
                    name="opening_balance"
                    class="form-control"
                    value="{{ old('opening_balance', $customer->opening_balance) }}"
                >

                @error('opening_balance')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>


            <div class="form-group">

                <div class="custom-control custom-switch">

                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        class="custom-control-input"
                        id="is_active"
                        {{ old('is_active', $customer->is_active) ? 'checked' : '' }}
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
                <i class="bi bi-save"></i>
                Update Customer
            </button>

            <a href="/customers" class="btn btn-secondary">
                Cancel
            </a>

        </div>

    </form>

</div>

@stop