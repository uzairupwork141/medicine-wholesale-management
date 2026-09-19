@extends('adminlte::page')

@section('title', 'Customers')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Customers</h1>

        <a href="/customers/create" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Add Customer
        </a>
    </div>
@stop

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">
            Customer List
        </h3>
    </div>

    <div class="card-body">

        <table class="table table-bordered table-hover">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer Code</th>
                    <th>Business Name</th>
                    <th>Contact Person</th>
                    <th>Phone</th>
                    <th>Credit Limit</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse($customers as $customer)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $customer->customer_code }}
                        </td>

                        <td>
                            {{ $customer->business_name }}
                        </td>

                        <td>
                            {{ $customer->contact_person }}
                        </td>

                        <td>
                            {{ $customer->phone }}
                        </td>

                        <td>
                            Rs. {{ number_format($customer->credit_limit, 2) }}
                        </td>

                        <td>

                            @if($customer->is_active)

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

                           <a
                                href="/customers/{{ $customer->id }}/edit"
                                class="btn btn-sm btn-warning"
                            >
                                <i class="bi bi-pencil"></i>
                            </a>

                            <form
                                action="/customers/{{ $customer->id }}"
                                method="POST"
                                style="display: inline;"
                                onsubmit="return confirm('Are you sure you want to delete this customer?');"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn btn-sm btn-danger"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="8" class="text-center">
                            No customers found.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@stop