@extends('layouts.admin')

@section('title', 'Change Password')

@section('content_header')
    <h1>Change Password</h1>
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
                    <h3 class="card-title">Change Password</h3>
                </div>


                <div class="card-body">

                    <form
                        action="{{ route('profile.password.update') }}"
                        method="POST"
                    >

                        @csrf
                        @method('PUT')


                        <div class="form-group mb-3">

                            <label>Current Password</label>

                            <input
                                type="password"
                                name="current_password"
                                class="form-control"
                                placeholder="Enter current password"
                                required
                            >

                        </div>


                        <div class="form-group mb-3">

                            <label>New Password</label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Enter new password"
                                required
                            >

                        </div>


                        <div class="form-group mb-3">

                            <label>Confirm New Password</label>

                            <input
                                type="password"
                                name="password_confirmation"
                                class="form-control"
                                placeholder="Confirm new password"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Change Password
                        </button>


                        <a
                            href="{{ route('profile') }}"
                            class="btn btn-secondary"
                        >
                            Back to Profile
                        </a>

                    </form>

                </div>

            </div>

        </div>

    </div>

@stop