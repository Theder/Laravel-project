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
            <form class="login-form" action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="card mb-0">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="icon-spinner11 icon-2x text-warning border-warning border-3 rounded-round p-3 mb-3 mt-1"></i>
                            <h5 class="mb-0">Password recovery</h5>
                            <span class="d-block text-muted">We'll send you instructions in email</span>
                        </div>

                        @if (\Session::has('status'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                            {{ \Session::get('status') }}
                        </div>
                        @endif

                        <div class="form-group form-group-feedback form-group-feedback-right">
                            <input type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                name="email"
                                placeholder="Your email">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            <div class="form-control-feedback">
                                <i class="icon-mail5 text-muted"></i>
                            </div>
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


