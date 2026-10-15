@php($active = 'api-client')
@extends('admin.layouts.master')

@section('title', 'Create API Client')

@section('content')
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Create API Client</h2>
            <a href="{{ route('admin.api-client.index') }}" class="btn btn-sm text-light btn-outline-secondary">
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

            <form method="POST" action="{{ route('admin.api-client.store') }}" novalidate>
                @csrf

                @include('admin.internal-api-client.field', ['mode' => 'create'])


                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.api-client.index') }}" class="btn btn-outline-secondary">Cancel</a>
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