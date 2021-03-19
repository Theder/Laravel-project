<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    @include('parts.panel.head')
</head>
<body>
    <div id="app">
        @include('parts.panel.navbar')

        <!-- Page content -->
	    <div class="page-content">
            @include('parts.panel.sidebar_menu')
            
            <!-- Main content -->
		    <div class="content-wrapper">
                @include('parts.panel.page_header')

                <!-- Content area -->
                <div class="content">
                    @yield('content')
                </div>
                <!-- /content area -->

                @include('parts.panel.footer')
            </div>
            <!-- /main content -->
        </div>
        <!-- /Page content -->

        @include('cookieConsent::index')
    </div>
</body>
</html>