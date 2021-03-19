@extends('layouts.auth')

@section('title')
Confirm password
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
                            <h5 class="mb-0">Confirm password</h5>
                            <span class="d-block text-muted">
                                This is a secure area of the application. Please confirm your password before continuing.
                            </span>
                        </div>

                        @if (\Session::has('status'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                            {{ \Session::get('status') }}
                        </div>
                        @endif

                        <div class="form-group form-group-feedback form-group-feedback-right">
                            <input type="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                name="password"
                                placeholder="Your password">
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            <div class="form-control-feedback">
                                <i class="icon-mail5 text-muted"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn bg-blue btn-block">
                            <i class="icon-spinner11 mr-2"></i> Confirm
                        </button>
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


{{--
<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <!-- Password -->
            <div>
                <x-label for="password" :value="__('Password')" />

                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
            </div>

            <div class="flex justify-end mt-4">
                <x-button>
                    {{ __('Confirm') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
--}}