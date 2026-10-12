@php($active = 'api-client')
@extends('admin.layouts.master')

@section('title', 'Create API Client')

@section('content')
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Create New User</h2>
            <a href="{{ route('admin.user.index') }}" class="btn btn-sm text-light btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="card-body card-bg-secondary">
            <form method="POST" action="{{ route('admin.api-client.store') }}" novalidate>
                @csrf

                {{-- Name (also used as the X-SGDP-Source header value) --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input
                        type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name') }}" maxlength="100" required
                        placeholder="e.g. POS Backend Service"
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input
                        type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email') }}" maxlength="100" required
                        placeholder="e.g. user@example.com"
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input
                        type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" value="{{ old('password') }}" maxlength="100" required
                        placeholder="Must be at least 10 characters long"
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input
                        type="password" class="form-control @error('confirm_password') is-invalid @enderror" id="confirm_password"
                        name="confirm_password" value="{{ old('confirm_password') }}" maxlength="100" required
                        placeholder="Must match the password"
                    >
                    @error('confirm_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Role --}}
                <div class="mb-3">
                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" id="role"
                            name="role" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" @selected(old('role') === $role->name)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status"
                            name="status" required>
                        <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                        <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                    </select>
                    <div class="form-text">
                        Only <strong>active</strong> users can be authenticated to login this portal.
                    </div>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.user.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-plus me-1"></i> Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection


@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Select2 tag input for allowed IPs.


        // Copy-to-clipboard buttons for the one-time credentials panel.
        document.querySelectorAll('[data-copy-target]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.querySelector(btn.getAttribute('data-copy-target'));
                if (!input) {
                    return;
                }

                navigator.clipboard.writeText(input.value).then(function () {
                    if (window.Swal) {
                        window.Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Copied to clipboard',
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    }
                });
            });
        });
    });
</script>
@endsection