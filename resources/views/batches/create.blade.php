@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Add Batch</h1>

        <a href="{{ route('batches.index') }}" class="btn btn-secondary">
            Back
        </a>
    </div>


    <div class="card">

        <div class="card-body">

            <form action="{{ route('batches.store') }}" method="POST">

                @csrf

                <div class="row">

                    {{-- Medicine Search --}}
                    <div class="col-md-6 mb-3">

                        <label for="medicine_search" class="form-label">
                            Medicine
                        </label>

                        <input type="text"
                               id="medicine_search"
                               class="form-control @error('medicine_id') is-invalid @enderror"
                               placeholder="Search medicine by name, code or barcode..."
                               autocomplete="off"
                               value="{{ old('medicine_id') ? ($medicines->firstWhere('id', old('medicine_id'))->name ?? '') : '' }}">

                        {{-- Actual Medicine ID --}}
                        <input type="hidden"
                               name="medicine_id"
                               id="medicine_id"
                               value="{{ old('medicine_id') }}">

                        {{-- Search Results --}}
                        <div id="medicine_results"
                             class="list-group mt-1"
                             style="display: none; max-height: 250px; overflow-y: auto;">
                        </div>

                        {{-- Selected Medicine --}}
                        <small id="selected_medicine"
                               class="text-success">

                            @if(old('medicine_id'))
                                ✓ Medicine selected
                            @endif

                        </small>

                        @error('medicine_id')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Batch Number --}}
                    <div class="col-md-6 mb-3">

                        <label for="batch_no" class="form-label">
                            Batch Number
                        </label>

                        <input type="text"
                               name="batch_no"
                               id="batch_no"
                               class="form-control @error('batch_no') is-invalid @enderror"
                               value="{{ old('batch_no') }}"
                               placeholder="Enter batch number">

                        @error('batch_no')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Manufacturing Date --}}
                    <div class="col-md-6 mb-3">

                        <label for="manufacturing_date" class="form-label">
                            Manufacturing Date
                        </label>

                        <input type="date"
                               name="manufacturing_date"
                               id="manufacturing_date"
                               class="form-control @error('manufacturing_date') is-invalid @enderror"
                               value="{{ old('manufacturing_date') }}">

                        @error('manufacturing_date')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Expiry Date --}}
                    <div class="col-md-6 mb-3">

                        <label for="expiry_date" class="form-label">
                            Expiry Date
                        </label>

                        <input type="date"
                               name="expiry_date"
                               id="expiry_date"
                               class="form-control @error('expiry_date') is-invalid @enderror"
                               value="{{ old('expiry_date') }}">

                        @error('expiry_date')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Purchase Price --}}
                    <div class="col-md-4 mb-3">

                        <label for="purchase_price" class="form-label">
                            Purchase Price
                        </label>

                        <input type="number"
                               step="0.01"
                               min="0"
                               name="purchase_price"
                               id="purchase_price"
                               class="form-control @error('purchase_price') is-invalid @enderror"
                               value="{{ old('purchase_price') }}"
                               placeholder="0.00">

                        @error('purchase_price')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Sale Price --}}
                    <div class="col-md-4 mb-3">

                        <label for="sale_price" class="form-label">
                            Sale Price
                        </label>

                        <input type="number"
                               step="0.01"
                               min="0"
                               name="sale_price"
                               id="sale_price"
                               class="form-control @error('sale_price') is-invalid @enderror"
                               value="{{ old('sale_price') }}"
                               placeholder="0.00">

                        @error('sale_price')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- MRP --}}
                    <div class="col-md-4 mb-3">

                        <label for="mrp" class="form-label">
                            MRP
                        </label>

                        <input type="number"
                               step="0.01"
                               min="0"
                               name="mrp"
                               id="mrp"
                               class="form-control @error('mrp') is-invalid @enderror"
                               value="{{ old('mrp') }}"
                               placeholder="0.00">

                        @error('mrp')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Quantity --}}
                    <div class="col-md-6 mb-3">

                        <label for="quantity" class="form-label">
                            Quantity
                        </label>

                        <input type="number"
                               min="0"
                               name="quantity"
                               id="quantity"
                               class="form-control @error('quantity') is-invalid @enderror"
                               value="{{ old('quantity', 0) }}"
                               placeholder="Enter quantity">

                        @error('quantity')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Status --}}
                    <div class="col-md-6 mb-3">

                        <label for="status" class="form-label">
                            Status
                        </label>

                        <select name="status"
                                id="status"
                                class="form-control @error('status') is-invalid @enderror">

                            <option value="Available"
                                {{ old('status', 'Available') == 'Available' ? 'selected' : '' }}>
                                Available
                            </option>

                            <option value="Expired"
                                {{ old('status') == 'Expired' ? 'selected' : '' }}>
                                Expired
                            </option>

                            <option value="Quarantine"
                                {{ old('status') == 'Quarantine' ? 'selected' : '' }}>
                                Quarantine
                            </option>

                        </select>

                        @error('status')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Buttons --}}
                <div class="mt-3">

                    <button type="submit"
                            class="btn btn-primary">
                        Save Batch
                    </button>

                    <a href="{{ route('batches.index') }}"
                       class="btn btn-secondary">
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>


<script>

    const form = document.querySelector('form');

    const medicines = @json($medicines);

    const searchInput = document.getElementById('medicine_search');

    const medicineIdInput = document.getElementById('medicine_id');

    const resultsBox = document.getElementById('medicine_results');

    const selectedMedicine = document.getElementById('selected_medicine');


    /*
    |--------------------------------------------------------------------------
    | Prevent Enter From Submitting Form
    |--------------------------------------------------------------------------
    |
    | Pressing Enter anywhere inside this form will NOT submit the form.
    | The form can only be submitted by clicking "Save Batch".
    |
    */

    form.addEventListener('keydown', function (event) {

        if (event.key === 'Enter') {

            event.preventDefault();

            return false;

        }

    });


    /*
    |--------------------------------------------------------------------------
    | Medicine Search
    |--------------------------------------------------------------------------
    */

    searchInput.addEventListener('input', function () {

        const search = this.value.toLowerCase().trim();


        // Remove previously selected medicine
        medicineIdInput.value = '';

        selectedMedicine.textContent = '';

        resultsBox.innerHTML = '';


        // If search is empty
        if (search.length === 0) {

            resultsBox.style.display = 'none';

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Find Medicines
        |--------------------------------------------------------------------------
        */

        const filteredMedicines = medicines.filter(function (medicine) {

            return (

                // Search by medicine name
                (
                    medicine.name &&
                    medicine.name.toLowerCase().includes(search)
                )

                ||

                // Search by product code
                (
                    medicine.product_code &&
                    medicine.product_code.toLowerCase().includes(search)
                )

                ||

                // Search by barcode
                (
                    medicine.barcode &&
                    medicine.barcode.toLowerCase().includes(search)
                )

            );

        });


        /*
        |--------------------------------------------------------------------------
        | No Medicine Found
        |--------------------------------------------------------------------------
        */

        if (filteredMedicines.length === 0) {

            resultsBox.innerHTML = `
                <div class="list-group-item text-danger">
                    No medicine found
                </div>
            `;

            resultsBox.style.display = 'block';

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Display Search Results
        |--------------------------------------------------------------------------
        */

        filteredMedicines.forEach(function (medicine) {

            const item = document.createElement('button');

            item.type = 'button';

            item.className =
                'list-group-item list-group-item-action';


            item.innerHTML = `

                <strong>
                    ${medicine.name}
                </strong>

                ${
                    medicine.product_code
                    ? `
                        <small class="text-muted">
                            &nbsp; | Code: ${medicine.product_code}
                        </small>
                    `
                    : ''
                }

                ${
                    medicine.barcode
                    ? `
                        <small class="text-muted">
                            &nbsp; | Barcode: ${medicine.barcode}
                        </small>
                    `
                    : ''
                }

            `;


            /*
            |--------------------------------------------------------------------------
            | Select Medicine
            |--------------------------------------------------------------------------
            */

            item.addEventListener('click', function () {

                searchInput.value = medicine.name;

                medicineIdInput.value = medicine.id;

                selectedMedicine.textContent =
                    '✓ Medicine selected';

                resultsBox.style.display = 'none';

                resultsBox.innerHTML = '';

            });


            resultsBox.appendChild(item);

        });


        resultsBox.style.display = 'block';

    });


    /*
    |--------------------------------------------------------------------------
    | Form Validation
    |--------------------------------------------------------------------------
    |
    | This runs ONLY when the user clicks Save Batch.
    |
    */

    form.addEventListener('submit', function (event) {

        /*
        |--------------------------------------------------------------------------
        | Check Medicine Selection
        |--------------------------------------------------------------------------
        */

        if (!medicineIdInput.value) {

            event.preventDefault();

            alert('Please search and select a valid medicine.');

            searchInput.focus();

            return;

        }

    });


    /*
    |--------------------------------------------------------------------------
    | Close Search Results When Clicking Outside
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', function (event) {

        if (
            !searchInput.contains(event.target) &&
            !resultsBox.contains(event.target)
        ) {

            resultsBox.style.display = 'none';

        }

    });

</script>

@endsection