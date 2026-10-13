@php($active = 'user')
@extends('admin.layouts.master')

@section('title', 'Edit User')

@section('content')


<div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Profle</h2>
        </div>

        @include('admin.user.field', ['mode' => 'edit'])

        <div class="mb-3 me-3 d-flex justify-content-end gap-2">
            <a href="{{ route('admin.user.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" form="userForm" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk me-1"></i> Save
            </button>
        </div>
    </div>
@endsection

@section('styles')


@endsection

@section('javascript')

<script type="text/javascript">
    $(document).ready(function () {




});
</script>
@endsection