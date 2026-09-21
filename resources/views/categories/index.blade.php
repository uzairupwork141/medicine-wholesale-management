@extends('layouts.admin')

@section('title', 'Categories')

@section('content_header')
    <h1>Categories</h1>
@stop

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">Categories List</h3>

        <div class="card-tools">
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                Add Category
            </a>
        </div>
    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @foreach($categories as $category)

                <tr>
                    <td>{{ $category->id }}</td>

                    <td>{{ $category->name }}</td>

                    <td>{{ $category->description }}</td>

                    <td>
                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                    </td>

                    <td>

                        <a href="{{ route('categories.edit', $category->id) }}"
                           class="btn btn-sm btn-warning">
                            Edit
                        </a>

                        <form action="{{ route('categories.destroy', $category->id) }}"
                              method="POST"
                              style="display:inline;">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this category?')">
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