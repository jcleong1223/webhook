@php($active = 'user')
@extends('admin.layouts.master')

@section('title', 'Admin Users')

@section('content')
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Admin Users</h2>
            <a href="{{ route('admin.user.create') }}" class="btn btn-primary">Create New User</a>
        </div>

        <div class="card-body card-bg-secondary ">
            <div class="table-responsive">
                <table id="user-table" class="table table-hover table-responsive mb-0 w-100">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>
@endsection


@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#user-table').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            paging: true,
            lengthChange: true,
            lengthMenu: [10, 25, 50, 100],
            pageLength: 25,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('admin.user.datatable') }}",
                type: "GET",
            },
            layout: {
                topStart: 'buttons',
                topEnd: 'search',
                bottomStart: ['pageLength', 'info'],
                bottomEnd: 'paging',
            },
            buttons: ['csv', 'excel', 'print'],
            columnDefs: [
                { orderable: false, targets: [5] },
            ],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name', orderable: true, searchable: true },
                { data: 'email', name: 'email', orderable: true, searchable: true},
                { data: 'role', name: 'role', orderable: true, searchable: true },
                { data: 'status', name: 'status', orderable: true, searchable: true },
                { data: 'created_at', name: 'created_at', orderable: true, searchable: true },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
    });
</script>
@endsection