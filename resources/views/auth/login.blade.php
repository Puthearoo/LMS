@extends('layouts.app')

@section('content')
<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary: #4361ee;
        --secondary: #3f37c9;
        --success: #4cc9f0;
        --light: #f8f9fa;
        --dark: #212529;
        --accent: #7209b7;
        --warning: #f72585;
        --gradient: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        --gradient-light: linear-gradient(135deg, #4cc9f0 0%, #4361ee 100%);
    }

    body {
        background: var(--light);
    }

    .login-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%234361ee' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
    }

    .login-container {
        width: 100%;
        max-width: 480px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .login-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        overflow: hidden;
        background: white;
    }

    .login-header {
        background: var(--gradient);
        color: white;
        padding: 2.5rem 2rem;
        text-align: center;
        position: relative;
    }

    .login-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
    }

    .login-icon {
        width: 70px;
        height: 70px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        backdrop-filter: blur(10px);
    }

    .login-icon i {
        font-size: 2rem;
        color: white;
    }

    .login-title {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .login-subtitle {
        margin-top: 0.5rem;
        opacity: 0.9;
        font-size: 0.95rem;
        position: relative;
        z-index: 1;
    }

    .login-body {
        padding: 2.5rem 2rem;
    }

    .form-label {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }

    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s;
        font-size: 0.95rem;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
    }

    .invalid-feedback {
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    .form-check-input {
        width: 1.2rem;
        height: 1.2rem;
        margin-top: 0.15rem;
        border: 2px solid #dee2e6;
        cursor: pointer;
    }

    .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .form-check-label {
        margin-left: 0.5rem;
        cursor: pointer;
        font-size: 0.95rem;
    }

    .btn-login {
        background: var(--gradient);
        border: none;
        color: white;
        padding: 0.85rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s;
        width: 100%;
        margin-top: 1rem;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 7px 20px rgba(67, 97, 238, 0.3);
        color: white;
    }

    .btn-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s;
        font-size: 0.9rem;
    }

    .btn-link:hover {
        color: var(--secondary);
        text-decoration: none;
    }

    .login-footer {
        text-align: center;
        padding: 1.5rem 2rem;
        background: var(--light);
        border-top: 1px solid #e9ecef;
    }

    .login-footer p {
        margin: 0;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .login-footer a {
        color: var(--primary);
        font-weight: 600;
        text-decoration: none;
        transition: color 0.3s;
    }

    .login-footer a:hover {
        color: var(--secondary);
    }

    .alert {
        border: none;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .alert-danger {
        background-color: #fee;
        color: #dc3545;
    }

    .alert-success {
        background-color: #d1f2eb;
        color: #0f5132;
    }

    /* Input Icons */
    .input-group {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 10;
        pointer-events: none;
    }

    .input-with-icon {
        padding-left: 2.8rem;
    }

    /* Responsive */
    @media (max-width: 576px) {
        .login-container {
            padding: 0 10px;
        }

        .login-header {
            padding: 2rem 1.5rem;
        }

        .login-title {
            font-size: 1.5rem;
        }

        .login-subtitle {
            font-size: 0.9rem;
        }

        .login-body {
            padding: 2rem 1.5rem;
        }

        .login-footer {
            padding: 1.5rem;
        }

        .btn-login {
            padding: 0.75rem 1.5rem;
        }
    }
</style>

<div class="login-wrapper">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-book-reader"></i>
                </div>
                <h1 class="login-title">Welcome Back</h1>
                <p class="login-subtitle">Sign in to access your digital library</p>
            </div>

            <div class="login-body">
                @if(session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            Email Address
                        </label>
                        <div class="input-group">
                            <input id="email" 
                                   type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="email" 
                                   autofocus
                                   placeholder="Enter your email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Password
                        </label>
                        <div class="input-group">
                            <input id="password" 
                                   type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="Enter your password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="remember" 
                                   id="remember" 
                                   {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Remember Me
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login">
                        Sign In
                    </button>

                    @if (Route::has('password.request'))
                        <div class="text-center mt-3">
                            <a class="btn-link" href="{{ route('password.request') }}">
                                Forgot Your Password?
                            </a>
                        </div>
                    @endif
                </form>
            </div>

            @if (Route::has('register'))
                <div class="login-footer">
                    <p>
                        Don't have an account?<br>
                        <a href="{{ route('register') }}">
                            Create Account
                        </a>
                    </p>
                </div>
            @endif
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('welcome') }}" class="btn-link">
                Back to Home
            </a>
        </div>
    </div>
</div>
@endsection