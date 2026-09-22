@extends('layouts.admin')

@section('title', 'Medicines')

@section('content_header')
    <h1>Medicines</h1>
@stop

@section('content')

<div class="card">

    <div class="card-header">

        <h3 class="card-title">
            Medicine List
        </h3>

        <div class="card-tools">

            <a href="{{ route('medicines.create') }}"
               class="btn btn-primary">
                Add Medicine
            </a>

        </div>

    </div>

    <div class="card-body">

        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Code</th>
                    <th>Medicine</th>
                    <th>Generic Name</th>
                    <th>Category</th>
                    <th>Manufacturer</th>
                    <th>MRP</th>
                    <th>Sale Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse($medicines as $medicine)

                    <tr>

                        <td>{{ $medicine->id }}</td>

                        <td>{{ $medicine->product_code }}</td>

                        <td>{{ $medicine->name }}</td>

                        <td>{{ $medicine->generic_name }}</td>

                        <td>
                            {{ $medicine->category->name ?? 'N/A' }}
                        </td>

                        <td>
                            {{ $medicine->manufacturer->name ?? 'N/A' }}
                        </td>

                        <td>{{ $medicine->mrp }}</td>

                        <td>{{ $medicine->default_sale_price }}</td>

                        <td>
                            @if($medicine->is_active)
                                <span class="badge badge-success">
                                    Active
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        <td>

                            <a href="{{ route('medicines.edit', $medicine->id) }}"
                               class="btn btn-sm btn-warning">
                                Edit
                            </a>

                            <form action="{{ route('medicines.destroy', $medicine->id) }}"
                                  method="POST"
                                  style="display:inline;">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this medicine?')">
                                    Delete
                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="10" class="text-center">
                            No medicines found.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@stop