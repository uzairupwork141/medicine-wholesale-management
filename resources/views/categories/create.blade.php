@extends('layouts.admin')

@section('title', isset($category) ? 'Edit Category' : 'Add Category')

@section('content_header')
    <h1>{{ isset($category) ? 'Edit Category' : 'Add Category' }}</h1>
@stop

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">
            {{ isset($category) ? 'Edit Category' : 'Add New Category' }}
        </h3>
    </div>

    <div class="card-body">

        <form action="{{ isset($category) ? route('categories.update', $category->id) : route('categories.store') }}"
              method="POST">

            @csrf

            @if(isset($category))
                @method('PUT')
            @endif

            <div class="form-group mb-3">

                <label>Name</label>

                <input type="text"
                       name="name"
                       class="form-control"
                       placeholder="Enter category name"
                       value="{{ $category->name ?? '' }}">

            </div>

            <div class="form-group mb-3">

                <label>Description</label>

                <textarea name="description"
                          class="form-control"
                          placeholder="Enter description">{{ $category->description ?? '' }}</textarea>

            </div>

            <div class="form-group mb-3">

                <label>Status</label>

                <select name="is_active" class="form-control">

                    <option value="1"
                        {{ isset($category) && $category->is_active == 1 ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="0"
                        {{ isset($category) && $category->is_active == 0 ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>

            </div>

            @if(isset($category))

                <button type="submit" class="btn btn-success">
                    Update Category
                </button>

            @else

                <button type="submit" class="btn btn-primary">
                    Save Category
                </button>

            @endif

            <a href="{{ route('categories.index') }}"
               class="btn btn-secondary">
                Back
            </a>

        </form>

    </div>

</div>

@stop