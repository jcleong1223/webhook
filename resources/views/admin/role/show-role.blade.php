@php($active = 'role-permission')
@extends('admin.layouts.master')

@section('title', 'Role Permission')

@section('content')
    <div class="card overflow-hidden mb-5">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Role</h2>
            <div class="gap-2">
                <a href="{{ route('admin.role-permission.index') }}" class="text-light btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1 "></i> Back
                </a>
                <a href="{{ route('admin.role-permission.edit-role', $role->id) }}" class="text-warning btn btn-sm btn-outline-warning">
                    <i class="fa-solid fa-edit me-1 "></i> Edit
                </a>
            </div>
        </div>

        <div class="card-body card-bg-secondary">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="row mb-4">
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Name</span>
                                    <input type="hidden" id="role_id" name="role_id" value={{ $role->id ?? '-' }}>
                                    <input
                                        type="text" class="form-control bg-secondary mt-2" value={{ $role->name ?? '-' }} readonly
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Permission List --}}
    <div class="card overflow-hidden mt-5">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Permission List ({{ $role->permissions->count() }})</h2>
            <a class="btn btn-sm btn-outline-secondary text-light collapsible"
                data-bs-toggle="collapse"
                href="#collapsePermissionList"
                role="button"
                aria-expanded="false"
                aria-controls="collapsePermissionList"
                data-click-type="permission-collapse-click"
            >
                Expand
            </a>
        </div>


        <div class="collapse" id="collapsePermissionList">
            <div class="card card-body">
                <div class="table-responsive">
                    <table id="permission-list-table" class="table table-hover table-responsive w-100">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Key</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    {{-- User List --}}
    <div class="card overflow-hidden mt-5">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">User List ({{ $role->users->count() }})</h2>
            <a class="btn btn-sm btn-outline-secondary text-light collapsible"
                data-bs-toggle="collapse"
                href="#collapseUserList"
                role="button"
                aria-expanded="false"
                aria-controls="collapseUserList"
                data-click-type="user-collapse-click"
            >
                Expand
            </a>
        </div>


        <div class="collapse" id="collapseUserList">
            <div class="card card-body">
                <div class="table-responsive">
                    <table id="user-list-table" class="table table-hover table-responsive w-100">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="alertConfigShowModal" tabindex="-1" role="dialog" aria-labelledby="alertConfigShowModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h5 class="modal-title" id="alertConfigShowModalLabel"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre class="mb-0"><code id="alertConfigContent" class="text-break"></code></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        $('#user-list-table').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            paging: true,
            lengthChange: true,
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            searching: true,
            ordering: true,
            order: [
                [4, 'desc']
            ],
            info: true,
            dom: 'Bfrtip',
            autoWidth: false,
            ajax: {
                url: "{{ route('admin.user.datatable') }}",
                type: "GET",
                data: function(d) {
                    d.role_id = $('#role_id').val();
                },
            },
            layout: {
                topStart: 'pageLength',
                topEnd: 'search',
                bottomStart: ['info'],
                bottomEnd: 'paging',
            },
            columnDefs: [{
                orderable: true,
                targets: [1]
            }, ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'email',
                    name: 'email',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    orderable: true,
                    searchable: false
                },
            ],
        });

        $('#permission-list-table').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            paging: true,
            lengthChange: true,
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            searching: true,
            ordering: true,
            order: [
                [2, 'desc']
            ],
            info: true,
            dom: 'Bfrtip',
            autoWidth: false,
            ajax: {
                url: "{{ route('admin.role-permission.permission-datatable') }}",
                type: "GET",
                data: function(d) {
                    d.role_id = $('#role_id').val();
                },
            },
            layout: {
                topStart: 'pageLength',
                topEnd: 'search',
                bottomStart: ['info'],
                bottomEnd: 'paging',
            },
            columnDefs: [{
                orderable: true,
                targets: [1]
            }, ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    orderable: true,
                    searchable: false
                },
            ],
        });

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

        $(document).ready(function () {

            $('table#permission-list-table').css('font-size', '14px');
            $('table#user-list-table').css('font-size', '14px');

            $(document).on('click', '.collapsible', function () {
                var clickType = $(this).attr('data-click-type');
                var btn = $(this);

                if (clickType === 'user-collapse-click') {
                    var userCollapsible = $('#collapseUserList');

                    userCollapsible.on('shown.bs.collapse', function () {
                        btn.text('Collapse');
                    });

                    userCollapsible.on('hidden.bs.collapse', function () {
                        btn.text('Expand');
                    });
                } else if (clickType === 'permission-collapse-click') {
                    var permissionCollapsible = $('#collapsePermissionList');

                    permissionCollapsible.on('shown.bs.collapse', function () {
                        btn.text('Collapse');
                    });

                    permissionCollapsible.on('hidden.bs.collapse', function () {
                        btn.text('Expand');
                    });
                }
            });

        })
    });

</script>
@endsection