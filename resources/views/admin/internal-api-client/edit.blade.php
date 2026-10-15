@php($active = 'api-client')
@extends('admin.layouts.master')

@section('title', 'Edit API Client')

@section('content')
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Edit API Client</h2>
            <a href="{{ route('admin.api-client.index') }}" class="btn btn-sm text-light btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="card-body card-bg-secondary">
            {{-- One-time credentials panel, shown only right after a successful create --}}
            <form method="POST" action="{{ route('admin.api-client.update', $client->id) }}" novalidate>
                @csrf
                @include('admin.internal-api-client.field', ['mode' => 'edit'])

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.api-client.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save
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