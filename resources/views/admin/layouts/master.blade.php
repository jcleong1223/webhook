<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Admin Panel'))</title>

    {{-- Vite: Bootstrap, FontAwesome, Select2, DataTables + app.js --}}
    @vite(['resources/js/app.js'])
    {{ Laravel\Horizon\Horizon::css() }}
    {{-- {{ Laravel\Horizon\Horizon::js() }} --}}
    @stack('styles')
</head>
<body>
    <div class="container mb-5">
        {{-- Top navbar --}}
        <div class="d-flex align-items-center py-4 header">
            <a href="{{ url(config('horizon.path')) }}" class="logo d-flex align-items-center text-decoration-none">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="30" height="30">
                    <path class="" fill="red" d="M5.26176342 26.4094389C2.04147988 23.6582233 0 19.5675182 0 15c0-4.1421356 1.67893219-7.89213562 4.39339828-10.60660172C7.10786438 1.67893219 10.8578644 0 15 0c8.2842712 0 15 6.71572875 15 15 0 8.2842712-6.7157288 15-15 15-3.716753 0-7.11777662-1.3517984-9.73823658-3.5905611zM4.03811305 15.9222506C5.70084247 14.4569342 6.87195416 12.5 10 12.5c5 0 5 5 10 5 3.1280454 0 4.2991572-1.9569336 5.961887-3.4222502C25.4934253 8.43417206 20.7645408 4 15 4 8.92486775 4 4 8.92486775 4 15c0 .3105915.01287248.6181765.03811305.9222506z" />
                </svg>
                <h1 class="h4 mb-0 ms-2"><strong>SGDATAPOS</strong> Central Webhook</h1>
            </a>
        </div>


        {{-- Sidebar + content --}}
        <div class="row mt-4">
            <div class="col-2 sidebar ">
                @include('admin.layouts.sidebar', ['active' => $active ?? ''])
            </div>

            <main class="col-10">
                @yield('content')
            </main>

            {{-- <div class="col-10">
                @if ($isDownForMaintenance)
                    <div class="alert alert-warning">
                        This application is in "maintenance mode". Queued jobs may not be processed unless your worker is using the "force" flag.
                    </div>
                @endif

                <router-view></router-view>
            </div> --}}
        </div>

        {{-- Global flash messages (SweetAlert2) --}}
        @if (session()->has('success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    window.Swal.fire('Success!', @json(session('success')), 'success');
                });
            </script>
        @endif
    </div>

    @yield('script')
    @stack('scripts')
</body>
</html>