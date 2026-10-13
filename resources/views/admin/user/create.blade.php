@php($active = 'user')
@extends('admin.layouts.master')

@section('title', 'Create New User')

@section('content')
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Create New User</h2>
            <a href="{{ route('admin.user.index') }}" class="btn btn-sm text-light btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>

        @include('admin.user.field', ['mode' => 'create'])

        <div class="mb-3 me-3 d-flex justify-content-end gap-2">
            <a href="{{ route('admin.user.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" form="userForm" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1"></i> Confirm
            </button>
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