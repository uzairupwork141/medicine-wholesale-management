@extends('layouts.admin')

@section('title', isset($user) ? 'Edit User' : 'Add User')

@section('content_header')
    <h1>{{ isset($user) ? 'Edit User' : 'Add User' }}</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            {{ isset($user) ? 'Edit User' : 'Add New User' }}
        </h3>
    </div>

    <div class="card-body">

        <form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" method="POST">

            @csrf

            @if(isset($user))
                @method('PUT')
            @endif

            <div class="form-group mb-3">
                <label>Name</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       placeholder="Enter name"
                       value="{{ $user->name ?? '' }}">
            </div>

            <div class="form-group mb-3">
                <label>Email</label>
                <input type="email"
                       name="email"
                       class="form-control"
                       placeholder="Enter email"
                       value="{{ $user->email ?? '' }}">
            </div>

            <div class="form-group mb-3">
                <label>Password</label>
                <input type="password"
                       name="password"
                       class="form-control"
                       placeholder="{{ isset($user) ? 'Leave empty to keep current password' : 'Enter password' }}">
            </div>

            <div class="form-group mb-3">
                <label>Role</label>

                <select name="role" class="form-control">

                    <option value="">Select Role</option>

                    <option value="admin"
                        {{ isset($user) && $user->role == 'admin' ? 'selected' : '' }}>
                        Admin
                    </option>

                    <option value="seller"
                        {{ isset($user) && $user->role == 'seller' ? 'selected' : '' }}>
                        Seller
                    </option>

                </select>
            </div>

            <div class="form-group mb-3">
                <label>Status</label>

                <select name="is_active" class="form-control">

                    <option value="1"
                        {{ isset($user) && $user->is_active == 1 ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="0"
                        {{ isset($user) && $user->is_active == 0 ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>
            </div>

            @if(isset($user))
                <button type="submit" class="btn btn-success">
                    Update User
                </button>
            @else
                <button type="submit" class="btn btn-primary">
                    Save User
                </button>
            @endif

            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                Back
            </a>

        </form>

    </div>
</div>

@stop