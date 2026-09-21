@extends('layouts.admin')

@section('title', isset($manufacturer) ? 'Edit Manufacturer' : 'Add Manufacturer')

@section('content_header')
    <h1>{{ isset($manufacturer) ? 'Edit Manufacturer' : 'Add Manufacturer' }}</h1>
@stop

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">
            {{ isset($manufacturer) ? 'Edit Manufacturer' : 'Add New Manufacturer' }}
        </h3>
    </div>

    <div class="card-body">

        <form action="{{ isset($manufacturer) ? route('manufacturers.update', $manufacturer->id) : route('manufacturers.store') }}"
              method="POST">

            @csrf

            @if(isset($manufacturer))
                @method('PUT')
            @endif

            <div class="form-group mb-3">
                <label>Name</label>

                <input type="text"
                       name="name"
                       class="form-control"
                       placeholder="Enter manufacturer name"
                       value="{{ $manufacturer->name ?? '' }}">

            </div>

            <div class="form-group mb-3">
                <label>Phone</label>

                <input type="text"
                       name="phone"
                       class="form-control"
                       placeholder="Enter phone number"
                       value="{{ $manufacturer->phone ?? '' }}">

            </div>

            <div class="form-group mb-3">
                <label>Email</label>

                <input type="email"
                       name="email"
                       class="form-control"
                       placeholder="Enter email"
                       value="{{ $manufacturer->email ?? '' }}">

            </div>

            <div class="form-group mb-3">
                <label>Address</label>

                <textarea name="address"
                          class="form-control"
                          placeholder="Enter address">{{ $manufacturer->address ?? '' }}</textarea>

            </div>

            <div class="form-group mb-3">
                <label>Status</label>

                <select name="is_active" class="form-control">

                    <option value="1"
                        {{ isset($manufacturer) && $manufacturer->is_active == 1 ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="0"
                        {{ isset($manufacturer) && $manufacturer->is_active == 0 ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>

            </div>

            @if(isset($manufacturer))

                <button type="submit" class="btn btn-success">
                    Update Manufacturer
                </button>

            @else

                <button type="submit" class="btn btn-primary">
                    Save Manufacturer
                </button>

            @endif

            <a href="{{ route('manufacturers.index') }}"
               class="btn btn-secondary">
                Back
            </a>

        </form>

    </div>

</div>

@stop