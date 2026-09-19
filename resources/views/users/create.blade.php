@extends('layouts.admin')

@section('title', 'Add User')

@section('content_header')
    <h1>Add User</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Add New User</h3>
    </div>

    <div class="card-body">

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="form-group mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter name">
            </div>

            <div class="form-group mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="Enter email">
            </div>

            <div class="form-group mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password">
            </div>

            <div class="form-group mb-3">
                <label>Role</label>
                <select name="role" class="form-control">
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="seller">Seller</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label>Status</label>
                <select name="is_active" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                Save User
            </button>

            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                Back
            </a>

        </form>

    </div>
</div>

@stop