@extends('layouts.admin')

@section('title', 'Users')

@section('content_header')
    <h1>Users</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Users List</h3>

            <div class="card-tools">
               <a href="{{ route('users.create') }}" class="btn btn-primary">
                    Add User
                </a>
            </div>
        </div>

        <div class="card-body">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($users as $user)
    <tr>
        <td>{{ $user->id }}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ $user->role }}</td>
        <td>
            {{ $user->is_active ? 'Active' : 'Inactive' }}
        </td>
    </tr>
@endforeach
                </tbody>
            </table>

        </div>
    </div>

@stop