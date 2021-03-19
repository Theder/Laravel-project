@extends('layouts.auth')

@section('title')
Register
@endsection

@section('content')
<!-- Page content -->
<div class="page-content">

    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Content area -->
        <div class="content d-flex justify-content-center align-items-center">

            <!-- Registration form -->
            <form method="POST" class="login-form" action="{{ route('register') }}">
                @csrf
                <div class="card mb-0">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="icon-plus3 icon-2x text-success border-success border-3 rounded-round p-3 mb-3 mt-1"></i>
                            <h5 class="mb-0">Create account</h5>
                            <span class="d-block text-muted">All fields are required</span>
                        </div>

                        <div class="form-group text-center text-muted content-divider">
                            <span class="px-2">Your credentials</span>
                        </div>

                        <div class="form-group form-group-feedback form-group-feedback-left">
                            <input 
                                type="text" 
                                name="name" 
                                value="{{ old('name') }}" 
                                required 
                                autocomplete="name" autofocus
                                class="form-control @error('name') is-invalid @enderror" 
                                placeholder="Username">
                            <div class="form-control-feedback">
                                <i class="icon-user-check text-muted"></i>
                            </div>
                            @error('name')
                            <span class="form-text text-danger">
                                <i class="icon-cancel-circle2 mr-2"></i> 
                                {{ $message }}
                            </span>
                            @enderror
                        </div>

                        <div class="form-group form-group-feedback form-group-feedback-left">
                            <input type="password" 
                                name="password" required autocomplete="new-password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Password">
                            <div class="form-control-feedback">
                                <i class="icon-user-lock text-muted"></i>
                            </div>
                            @error('password')
                            <span class="form-text text-danger">
                                <i class="icon-cancel-circle2 mr-2"></i> 
                                {{ $message }}
                            </span>
                            @enderror
                        </div>

                        <div class="form-group form-group-feedback form-group-feedback-left">
                            <input type="password" 
                                name="password_confirmation" required
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                placeholder="Password">
                            <div class="form-control-feedback">
                                <i class="icon-user-lock text-muted"></i>
                            </div>
                            @error('password_confirmation')
                            <span class="form-text text-danger">
                                <i class="icon-cancel-circle2 mr-2"></i> 
                                {{ $message }}
                            </span>
                            @enderror
                        </div>

                        <div class="form-group text-center text-muted content-divider">
                            <span class="px-2">Your contacts</span>
                        </div>

                        <div class="form-group form-group-feedback form-group-feedback-left">
                            <input 
                                type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                name="email" 
                                value="{{ old('email') }}" required autocomplete="email"
                                placeholder="Your email">
                            <div class="form-control-feedback">
                                <i class="icon-mention text-muted"></i>
                            </div>
                            @error('email')
                            <span class="form-text text-danger">
                                <i class="icon-cancel-circle2 mr-2"></i> 
                                {{ $message }}
                            </span>
                            @enderror
                        </div>

                        <div class="form-group text-center text-muted content-divider">
                            <span class="px-2">Additions</span>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="checkbox" name="terms" class="form-check-input">
                                    Accept <a href="{{ route('terms') }}">terms of service</a>
                                </label>
                                @error('terms')
                                <span class="form-text text-danger">
                                    <i class="icon-cancel-circle2 mr-2"></i> 
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn bg-teal-400 btn-block">Register <i class="icon-circle-right2 ml-2"></i></button>
                    </div>
                </div>
            </form>
            <!-- /registration form -->

        </div>
        <!-- /content area -->

    </div>
    <!-- /main content -->

</div>
<!-- /page content -->

@endsection
