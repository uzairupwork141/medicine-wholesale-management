@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Batches</h1>

        <a href="{{ route('batches.create') }}" class="btn btn-primary">
            Add Batch
        </a>
    </div>

    <div class="card">

        <div class="card-body">

            <table class="table table-bordered table-striped">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Medicine</th>
                        <th>Batch No</th>
                        <th>Manufacturing Date</th>
                        <th>Expiry Date</th>
                        <th>Purchase Price</th>
                        <th>Sale Price</th>
                        <th>MRP</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($batches as $batch)

                        <tr>

                            <td>{{ $batch->id }}</td>

                            <td>
                                {{ $batch->medicine->name ?? 'N/A' }}
                            </td>

                            <td>
                                {{ $batch->batch_no }}
                            </td>

                            <td>
                                {{ $batch->manufacturing_date?->format('Y-m-d') }}
                            </td>

                            <td>
                                {{ $batch->expiry_date?->format('Y-m-d') }}
                            </td>

                            <td>
                                {{ $batch->purchase_price }}
                            </td>

                            <td>
                                {{ $batch->sale_price }}
                            </td>

                            <td>
                                {{ $batch->mrp }}
                            </td>

                            <td>
                                {{ $batch->quantity }}
                            </td>

                            <td>
                                {{ $batch->status }}
                            </td>

                            <td>

                                <a href="{{ route('batches.edit', $batch) }}"
                                   class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                <form action="{{ route('batches.destroy', $batch) }}"
                                      method="POST"
                                      style="display:inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this batch?')">
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="11" class="text-center">
                                No batches found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection