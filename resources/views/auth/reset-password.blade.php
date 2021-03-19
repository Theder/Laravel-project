@extends('layouts.auth')

@section('title')
Forgot password
@endsection

@section('page-styles')

@endsection

@section('content')
<div class="page-content">

    <!-- Main content -->
    <div class="content-wrapper">
        
        <!-- Content area -->
        <div class="content d-flex justify-content-center align-items-center">

            <!-- Password recovery form -->
            <form class="login-form" action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <div class="card mb-0">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="icon-spinner11 icon-2x text-warning border-warning border-3 rounded-round p-3 mb-3 mt-1"></i>
                            <h5 class="mb-0">Reset password</h5>
                        </div>

                        @if (\Session::has('status'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                            {{ \Session::get('status') }}
                        </div>
                        @endif

                        <div class="form-group form-group-feedback form-group-feedback-left">
                            <input 
                                type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                name="email" 
                                value="{{ old('email', $request->email) }}" required autocomplete="email"
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

                        <div class="form-group form-group-feedback form-group-feedback-left">
                            <input type="password" 
                                name="password" required autocomplete="new-password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="New password">
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
                                placeholder="Password confirmation">
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

                        <button type="submit" class="btn bg-blue btn-block"><i class="icon-spinner11 mr-2"></i> Reset password</button>
                    </div>
                </div>
            </form>
            <!-- /password recovery form -->

        </div>
        <!-- /content area -->

    </div>
    <!-- /main content -->

</div>
@endsection