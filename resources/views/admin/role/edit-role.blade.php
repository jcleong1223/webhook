@php($active = 'role-permission')
@extends('admin.layouts.master')

@section('title', 'Edit Role')

@section('content')
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Edit Role</h2>
            <a href="{{ route('admin.role-permission.index', ['tab' => 'role']) }}" class="btn btn-sm text-light btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>

        @include('admin.role.field', ['mode' => 'edit'])
    </div>
@endsection
