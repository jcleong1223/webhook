<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — {{ config('app.name', 'Admin Portal') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <div class="container-fluid p-0">
        <!-- g-0 removes grid gaps, min-vh-100 forces full screen height -->
        <div class="row g-0 min-vh-100">

            <!-- Left Side: Full-half background image -->
            <div class="col-md-6 d-none d-md-block position-relative overflow-hidden bg-dark">
                <img
                    src="https://cdn.prod.website-files.com/6a54ae55612b5a7d18ebec10/6a8c406aa91914dd72f07f1c_Abstract%20red%20circuit%20network%20symbolizing%20Sequel%20Data%20Systems%27%20IT%20connectivity.jpg"
                    alt="SGDATAPOS Banner"
                    class="w-100 h-100 position-absolute top-0 start-0"
                    style="object-fit: cover;"
                >
            </div>

            <!-- Right Side: Login Form -->
            <div class="col-md-6 d-flex align-items-center justify-content-center p-4 p-md-5">
                <div class="w-100" style="max-width: 380px;">

                    <div class="text-center mb-4">
                        <img src="{{ asset('asset/sgdatapos-logo-header.png') }}" alt="SGDATAPOS Logo" />
                        <h1 class="h5 mt-2 mb-0"><strong>SGDATAPOS</strong> Central Webhook</h1>
                        <p class="text-muted small mb-0">Admin Portal</p>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <p class="text-center mb-4 text-muted small">Kindly key in your credentials to continue</p>

                            @if ($errors->any())
                                <div class="alert alert-danger py-2 small mb-3">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                                           class="form-control @error('email') is-invalid @enderror"
                                           required autofocus autocomplete="username">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input id="password" type="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           required autocomplete="current-password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-check mb-3">
                                    <input id="remember" type="checkbox" name="remember" class="form-check-input">
                                    <label for="remember" class="form-check-label">Remember me</label>
                                </div>

                                <button type="submit" class="btn btn-danger w-100">Sign in</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</body>
</html>