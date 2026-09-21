@extends('layouts.admin')

@section('title', isset($medicine) ? 'Edit Medicine' : 'Add Medicine')

@section('content_header')
    <h1>{{ isset($medicine) ? 'Edit Medicine' : 'Add Medicine' }}</h1>
@stop

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">
            {{ isset($medicine) ? 'Edit Medicine' : 'Add New Medicine' }}
        </h3>
    </div>

    <div class="card-body">

        <form action="{{ isset($medicine) ? route('medicines.update', $medicine->id) : route('medicines.store') }}"
              method="POST">

            @csrf

            @if(isset($medicine))
                @method('PUT')
            @endif

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label>Product Code</label>
                    <input type="text"
                           name="product_code"
                           class="form-control"
                           value="{{ $medicine->product_code ?? '' }}"
                           placeholder="Enter product code">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Barcode</label>
                    <input type="text"
                           name="barcode"
                           class="form-control"
                           value="{{ $medicine->barcode ?? '' }}"
                           placeholder="Enter barcode">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Medicine Name</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ $medicine->name ?? '' }}"
                           placeholder="Enter medicine name">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Generic Name</label>
                    <input type="text"
                           name="generic_name"
                           class="form-control"
                           value="{{ $medicine->generic_name ?? '' }}"
                           placeholder="Enter generic name">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Category</label>

                    <select name="category_id" class="form-control">

                        <option value="">Select Category</option>

                        @foreach($categories as $category)

                            <option value="{{ $category->id }}"
                                {{ isset($medicine) && $medicine->category_id == $category->id ? 'selected' : '' }}>

                                {{ $category->name }}

                            </option>

                        @endforeach

                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Manufacturer</label>

                    <select name="manufacturer_id" class="form-control">

                        <option value="">Select Manufacturer</option>

                        @foreach($manufacturers as $manufacturer)

                            <option value="{{ $manufacturer->id }}"
                                {{ isset($medicine) && $medicine->manufacturer_id == $manufacturer->id ? 'selected' : '' }}>

                                {{ $manufacturer->name }}

                            </option>

                        @endforeach

                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Dosage Form</label>
                    <input type="text"
                           name="dosage_form"
                           class="form-control"
                           value="{{ $medicine->dosage_form ?? '' }}"
                           placeholder="Tablet / Syrup">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Strength</label>
                    <input type="text"
                           name="strength"
                           class="form-control"
                           value="{{ $medicine->strength ?? '' }}"
                           placeholder="500mg">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Pack Size</label>
                    <input type="text"
                           name="pack_size"
                           class="form-control"
                           value="{{ $medicine->pack_size ?? '' }}"
                           placeholder="10 tablets">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Unit</label>
                    <input type="text"
                           name="unit"
                           class="form-control"
                           value="{{ $medicine->unit ?? '' }}"
                           placeholder="Box / Bottle">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Default Sale Price</label>
                    <input type="number"
                           step="0.01"
                           name="default_sale_price"
                           class="form-control"
                           value="{{ $medicine->default_sale_price ?? '' }}"
                           placeholder="0.00">
                </div>

                <div class="col-md-4 mb-3">
                    <label>MRP</label>
                    <input type="number"
                           step="0.01"
                           name="mrp"
                           class="form-control"
                           value="{{ $medicine->mrp ?? '' }}"
                           placeholder="0.00">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Reorder Level</label>
                    <input type="number"
                           name="reorder_level"
                           class="form-control"
                           value="{{ $medicine->reorder_level ?? '' }}"
                           placeholder="Enter reorder level">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Status</label>

                    <select name="is_active" class="form-control">

                        <option value="1"
                            {{ isset($medicine) && $medicine->is_active == 1 ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="0"
                            {{ isset($medicine) && $medicine->is_active == 0 ? 'selected' : '' }}>
                            Inactive
                        </option>

                    </select>
                </div>

            </div>

            @if(isset($medicine))

                <button type="submit" class="btn btn-success">
                    Update Medicine
                </button>

            @else

                <button type="submit" class="btn btn-primary">
                    Save Medicine
                </button>

            @endif

            <a href="{{ route('medicines.index') }}" class="btn btn-secondary">
                Back
            </a>

        </form>

    </div>

</div>

@stop