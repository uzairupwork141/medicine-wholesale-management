@extends('layouts.admin')

@section('title', 'Manufacturers')

@section('content_header')
    <h1>Manufacturers</h1>
@stop

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">Manufacturers List</h3>

        <div class="card-tools">
            <a href="{{ route('manufacturers.create') }}" class="btn btn-primary">
                Add Manufacturer
            </a>
        </div>
    </div>

    <div class="card-body">

        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @foreach($manufacturers as $manufacturer)

                <tr>

                    <td>{{ $manufacturer->id }}</td>

                    <td>{{ $manufacturer->name }}</td>

                    <td>{{ $manufacturer->phone }}</td>

                    <td>{{ $manufacturer->email }}</td>

                    <td>{{ $manufacturer->address }}</td>

                    <td>
                        @if($manufacturer->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </td>

                    <td>

                        <a href="{{ route('manufacturers.edit', $manufacturer->id) }}"
                           class="btn btn-sm btn-warning">
                            Edit
                        </a>

                        <form action="{{ route('manufacturers.destroy', $manufacturer->id) }}"
                              method="POST"
                              style="display:inline;">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this manufacturer?')">
                                Delete
                            </button>

                        </form>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@stop