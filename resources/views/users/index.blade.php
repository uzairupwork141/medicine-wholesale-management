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
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
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

                            <td>

                                <a href="{{ route('users.edit', $user->id) }}"
                                   class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                <form action="{{ route('users.destroy', $user->id) }}"
                                      method="POST"
                                      style="display:inline;">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this user?')">
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