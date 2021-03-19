@extends('layouts.auth')

@section('title')
Login
@endsection

@section('page-styles')

@endsection

@section('content')
<!-- Page content -->
<div class="page-content">

    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Content area -->
        <div class="content d-flex justify-content-center align-items-center">

            <!-- Login card -->
            <form class="login-form" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="card mb-0">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="icon-reading icon-2x text-slate-300 border-slate-300 border-3 rounded-round p-3 mb-3 mt-1"></i>
                            <h5 class="mb-0">Login to your account</h5>
                            <span class="d-block text-muted">Your credentials</span>
                        </div>

                        <div class="form-group form-group-feedback form-group-feedback-left">
                            <input 
                                type="text" 
                                class="form-control @error('email') is-invalid @enderror" 
                                placeholder="Email"
                                name="email" 
                                value="{{ old('email') }}" 
                                required autocomplete="email" autofocus>
                            <div class="form-control-feedback">
                                <i class="icon-user text-muted"></i>
                            </div>
                            @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group form-group-feedback form-group-feedback-left">
                            <input type="password" 
                                class="form-control @error('email') is-invalid @enderror" 
                                placeholder="Password"
                                name="password" required autocomplete="current-password">
                            <div class="form-control-feedback">
                                <i class="icon-lock2 text-muted"></i>
                            </div>
                            @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group d-flex align-items-center">
                            <div class="form-check mb-0">
                                <label class="form-check-label">
                                    <input 
                                        type="checkbox" name="remember" class="form-check-input">
                                    Remember
                                </label>
                            </div>

                            <a href="{{ route('password.request') }}" class="ml-auto">Forgot password?</a>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Sign in <i class="icon-circle-right2 ml-2"></i></button>
                        </div>

                        <div class="form-group text-center text-muted content-divider">
                            <span class="px-2">or sign in with</span>
                        </div>

                        <div class="form-group text-center">
                            <a href="{{-- route('auth.facebook') --}}" class="btn btn-outline bg-indigo border-indigo text-indigo btn-icon rounded-round border-2">
                                <i class="icon-facebook"></i>
                            </a>
                            <a href="{{-- route('auth.google') --}}" class="btn btn-outline bg-pink-300 border-pink-300 text-pink-300 btn-icon rounded-round border-2 ml-2">
                                <i class="icon-google"></i>
                            </a>
                        </div>

                        <div class="form-group text-center text-muted content-divider">
                            <span class="px-2">Don't have an account?</span>
                        </div>

                        <div class="form-group">
                            <a href="{{ route('register') }}" class="btn btn-light btn-block">Sign up</a>
                        </div>

                        <span class="form-text text-center text-muted">
                            By continuing, you're confirming that you've read our 
                            <a href="{{ route('terms') }}">Terms &amp; Conditions</a> and 
                            <a href="{{ route('privacy') }}">Cookie Policy</a>
                        </span>
                    </div>
                </div>
            </form>
            <!-- /login card -->

        </div>
        <!-- /content area -->

    </div>
    <!-- /main content -->

</div>
<!-- /page content -->
@endsection
