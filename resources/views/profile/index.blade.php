@extends('layouts.admin')

@section('title', 'My Profile')

@section('content_header')
    <h1>My Profile</h1>
@stop

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="row">

        <div class="col-md-8">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">Profile Information</h3>
                </div>

                <div class="card-body">

                    <form action="{{ route('profile.update') }}" method="POST">

                        @csrf
                        @method('PUT')


                        <div class="form-group mb-3">

                            <label>Name</label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="{{ old('name', $user->name) }}"
                                required
                            >

                        </div>


                        <div class="form-group mb-3">

                            <label>Email</label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email', $user->email) }}"
                                required
                            >

                        </div>


                        <div class="form-group mb-3">

                            <label>Role</label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ ucfirst($user->role) }}"
                                readonly
                            >

                        </div>


                        <div class="form-group mb-3">

                            <label>Status</label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ $user->is_active ? 'Active' : 'Inactive' }}"
                                readonly
                            >

                        </div>


                        <button type="submit" class="btn btn-primary">
                            Update Profile
                        </button>


                        <a
                            href="{{ route('profile.password') }}"
                            class="btn btn-warning"
                        >
                            Change Password
                        </a>

                    </form>

                </div>

            </div>

        </div>

    </div>

@stop