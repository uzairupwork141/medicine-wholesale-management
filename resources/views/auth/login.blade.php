<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Medical Management - Login</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #e8f1ff, #f8fbff);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            width: 400px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            text-align: center;
            padding: 28px 20px;
        }

        .login-header .logo {
            width: 65px;
            height: 65px;
            background: white;
            color: #007bff;
            border-radius: 50%;
            margin: 0 auto 12px;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 30px;
            font-weight: bold;
        }

        .login-header h2 {
            margin: 0;
            font-size: 25px;
        }

        .login-header p {
            margin: 7px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .login-body {
            padding: 30px;
        }

        .welcome {
            text-align: center;
            margin-bottom: 25px;
        }

        .welcome h3 {
            margin: 0 0 6px;
            color: #333333;
            font-size: 21px;
        }

        .welcome p {
            margin: 0;
            color: #777777;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;

            font-size: 14px;
            font-weight: 600;
            color: #333333;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;

            border: 1px solid #d5dbe3;
            border-radius: 7px;

            font-size: 14px;
            outline: none;

            transition: 0.2s;
        }

        .form-control:focus {
            border-color: #007bff;

            box-shadow:
                0 0 0 3px rgba(0, 123, 255, 0.12);
        }

        .login-btn {
            width: 100%;
            padding: 12px;

            border: none;
            border-radius: 7px;

            background: #007bff;
            color: white;

            font-size: 16px;
            font-weight: 600;

            cursor: pointer;

            transition: 0.2s;
        }

        .login-btn:hover {
            background: #0056b3;
        }

        .error-message {
            background: #ffe5e5;
            color: #b00020;

            border: 1px solid #ffb8b8;
            border-radius: 6px;

            padding: 10px;
            margin-bottom: 20px;

            font-size: 14px;
        }

        .validation-errors {
            background: #fff3cd;
            color: #856404;

            border: 1px solid #ffeeba;
            border-radius: 6px;

            padding: 10px;
            margin-bottom: 20px;

            font-size: 14px;
        }

        .validation-errors ul {
            margin: 0;
            padding-left: 20px;
        }

        .login-footer {
            text-align: center;

            padding: 15px 20px;

            border-top: 1px solid #eeeeee;

            color: #777777;

            font-size: 12px;
        }

        @media (max-width: 500px) {

            .login-container {
                width: 90%;
            }

            .login-body {
                padding: 25px 20px;
            }

            .login-header h2 {
                font-size: 22px;
            }
        }
    </style>
</head>


<body>

    <div class="login-container">


        <!-- Header -->

        <div class="login-header">

            <div class="logo">
                💊
            </div>

            <h2>
                Medical Management
            </h2>

            <p>
                Wholesale Medical Management System
            </p>

        </div>


        <!-- Login Body -->

        <div class="login-body">


            <!-- Welcome -->

            <div class="welcome">

                <h3>
                    Welcome Back
                </h3>

                <p>
                    Sign in to access your account
                </p>

            </div>


            <!-- Login Error -->

            @if(session('error'))

                <div class="error-message">

                    {{ session('error') }}

                </div>

            @endif


            <!-- Validation Errors -->

            @if($errors->any())

                <div class="validation-errors">

                    <ul>

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <!-- Login Form -->

            <form action="{{ route('login.submit') }}" method="POST">

                @csrf


                <!-- Email -->

                <div class="form-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        placeholder="Enter your email"
                        value="{{ old('email') }}"
                        required
                    >

                </div>


                <!-- Password -->

                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Enter your password"
                        required
                    >

                </div>


                <!-- Login Button -->

                <button
                    type="submit"
                    class="login-btn"
                >
                    Login
                </button>


            </form>


        </div>


        <!-- Footer -->

        <div class="login-footer">

            Medical Management System
            © {{ date('Y') }}

        </div>


    </div>

</body>

</html>