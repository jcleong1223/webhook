@php($active = 'api-clients')
@extends('admin.layouts.master')

@section('title', 'Create API Client')

@section('content')
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Create API Client</h2>
            <a href="{{ route('admin.api-clients.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="card-body card-bg-secondary">
            {{-- One-time credentials panel, shown only right after a successful create --}}
            @if (session('new_client_id') && session('new_client_secret'))
                <div class="alert alert-success border-success">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fa-solid fa-circle-check me-2"></i>
                        <strong>API client created successfully.</strong>
                    </div>
                    <p class="mb-2 small">
                        Copy the credentials below now. For security, the <strong>secret</strong> is stored
                        encrypted and <strong>will not be shown again</strong>.
                    </p>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold mb-1">Client ID</label>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace bg-light text-dark" id="new-client-id"
                                   value="{{ session('new_client_id') }}" readonly>
                            <button class="btn btn-outline-secondary" type="button"
                                    data-copy-target="#new-client-id">Copy</button>
                        </div>
                    </div>
                    <div>
                        <label class="form-label small fw-semibold mb-1">Client Secret</label>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace bg-light text-dark" id="new-client-secret"
                                   value="{{ session('new_client_secret') }}" readonly>
                            <button class="btn btn-outline-secondary" type="button"
                                    data-copy-target="#new-client-secret">Copy</button>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.api-clients.store') }}" novalidate>
                @csrf

                {{-- Name (also used as the X-SGDP-Source header value) --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input
                        type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name') }}" maxlength="100" required
                        placeholder="e.g. POS Backend Service"
                    >
                    <div class="form-text">
                        Human-readable client name. This value is also sent as the
                        <code>X-SGDP-Source</code> request header.
                    </div>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Allowed IPs --}}
                <div class="mb-3">
                    <label for="allowed_ips" class="form-label">Allowed IP Addresses</label>
                    <textarea class="form-control @error('allowed_ips') is-invalid @enderror @error('allowed_ips.*') is-invalid @enderror"
                        id="allowed_ips"
                        name="allowed_ips"
                        rows="4"
                        placeholder="192.168.1.1&#10;10.0.0.1">{{ is_array(old('allowed_ips', $model->allowed_ips ?? null))
                            ? implode("\n", old('allowed_ips', $model->allowed_ips ?? null))
                            : old('allowed_ips', $model->allowed_ips ?? '') }}</textarea>

                    @error('allowed_ips.*')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
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
                        Only <strong>active</strong> clients can authenticate webhook requests.
                    </div>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Auto-generated credentials note --}}
                <div class="alert alert-light border small mb-4">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    The <strong>Client ID</strong> and <strong>Client Secret</strong> are generated
                    automatically when the client is created and shown to you only once.
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.api-clients.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-plus me-1"></i> Create API Client
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