@php($active = 'role-permission')
@extends('admin.layouts.master')

@section('title', 'Role and Permission')

@section('content')
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Role and Permission</h2>
        </div>

        <div class="card-body card-bg-secondary">
            <ul class="nav nav-tabs" id="role_perm_nav" role="tablist">

                @can('permission.view')
                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link {{ Request()->get('tab') != 'role' ? 'active text-light' : '' }}"
                        id="permission-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#permission"
                        type="button"
                        role="tab"
                        aria-controls="permission"
                        aria-selected="{{ Request()->get('tab') != 'role' ? 'true' : 'false' }}"
                    >
                        Permissions
                    </button>
                </li>
                @endcan

                @can('role.view')
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {{ Request()->get('tab') == 'role' ? 'active ' : 'text-light' }}"
                            id="role-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#role"
                            type="button"
                            role="tab"
                            aria-controls="role"
                            aria-selected="{{ Request()->get('tab') == 'role' ? 'true' : 'false' }}"
                        >
                            Roles
                        </button>
                    </li>
                @endcan

                @if(!auth()->user()->can('role.view') && !auth()->user()->can('permission.view'))
                <li class="nav-item" role="presentation">
                        <button
                            class="nav-link active"
                            id="empty-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#empty"
                            type="button"
                            role="tab"
                            aria-controls="empty"
                            aria-selected="{{ Request()->get('tab') == 'role' ? 'true' : 'false' }}"
                        >
                            Information
                        </button>
                    </li>
                @endif
            </ul>

            <div class="tab-content pt-3" id="role_perm_content">

                {{-- Permissions Tab --}}
                @can('permission.view')
                <div
                    class="tab-pane fade {{ Request()->get('tab') != 'role' ? 'show active' : '' }}"
                    id="permission"
                    role="tabpanel"
                    aria-labelledby="permission-tab"
                >
                    <div class="row g-3">


                        {{-- Create Permission --}}
                        <div class="col-md-4">
                            @can('permission.create')
                            <div class="info-box d-block p-3 h-100">
                                <h6 class="mb-3">
                                    <i class="fa-solid fa-shield-halved me-1"></i>
                                    Create Permission
                                </h6>

                                <form
                                    id="permissionForm"
                                    method="POST"
                                    action="{{ route('admin.role-permission.store-permission') }}"
                                >
                                    @csrf

                                    <div class="mb-3">
                                        <label for="module" class="form-label">
                                            Module
                                        </label>

                                        <select
                                            name="module"
                                            id="module"
                                            class="form-select"
                                            required
                                        >
                                            <option value="">Select module</option>

                                            @foreach($modules as $key => $module)
                                                <option value="{{ $key }}">
                                                    {{ $module['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="action" class="form-label">
                                            Action
                                        </label>

                                        <select
                                            name="action"
                                            id="action"
                                            class="form-select"
                                            required
                                            disabled
                                        >
                                            <option value="">Select module first</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="permission_preview" class="form-label">
                                            Permission Key Preview
                                        </label>

                                        <input
                                            type="text"
                                            id="permission_preview"
                                            class="form-control bg-secondary"
                                            placeholder="module.action"
                                            readonly
                                        >

                                        <small class="text-muted">
                                            Generated automatically from your selections.
                                        </small>
                                    </div>

                                    <div
                                        id="permission-form-message"
                                        class="alert d-none"
                                        role="alert"
                                    ></div>

                                    <div class="d-flex justify-content-end">
                                        <button
                                            type="submit"
                                            id="addPermissionBtn"
                                            class="btn btn-primary"
                                            disabled
                                        >
                                            <i class="fa-solid fa-plus me-1"></i>
                                            Add Permission
                                        </button>
                                    </div>
                                </form>
                            </div>
                            @endcan
                        </div>


                        {{-- Permission List --}}
                        <div class="col-md-8">
                            <div class="info-box d-block p-3 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="mb-0">
                                        <i class="fa-solid fa-list-check me-1"></i>
                                        Permission List
                                    </h6>

                                    <span class="badge bg-secondary">
                                        {{ $permissionCount }} permissions in total
                                    </span>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" id='permission-table'>
                                        <thead>
                                            <tr>
                                                <th>Permission Key</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody id="permissionTableBody">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                @endcan

                {{-- Roles Tab --}}
                @can('role.view')
                <div
                    class="tab-pane fade {{ Request()->get('tab') == 'role' ? 'show active' : '' }}"
                    id="role"
                    role="tabpanel"
                    aria-labelledby="role-tab"
                >
                    <div class="info-box d-flex align-items-center justify-content-between p-3">
                        <div class="d-block">
                            <h6>Role Management</h6>
                            <p class="text-muted mb-0">
                                Link permission keys to roles to grant the corresponding permissions for accessing specific modules and actions.
                            </p>
                        </div>
                        @can('role.create')
                        <div class="d-block">
                            <a href="{{ route('admin.role-permission.create-role') }}" class="btn btn-sm btn-primary">Create New Role</a>
                        </div>
                        @endcan
                    </div>

                    <div class="card overflow-hidden">
                        <div class="card-body card-bg-secondary ">
                            <div class="table-responsive">
                                <table id="user-table" class="table table-hover table-responsive mb-0 w-100">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Name</th>
                                            <th>Permission Count</th>
                                            <th>User Count</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($roles as $role)
                                            <tr>
                                                <td>{{ $role->id }}</td>
                                                <td>{{ $role->name }}</td>
                                                <td>{{ $role->permissions->count() }}</td>
                                                <td>{{ $role->users->count() }}</td>
                                                <td>{{ $role->created_at->format('F j, Y') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.role-permission.show-role', $role->id) }}" class="btn btn-sm btn-info"><i class="fa-solid fa-eye text-white"></i></a>

                                                    @can('role.update')
                                                        <a href="{{ route('admin.role-permission.edit-role', $role->id) }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-edit"></i></a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan

                {{-- Empty tab --}}
                @if(!auth()->user()->can('role.view') && !auth()->user()->can('permission.view'))
                <div
                    class="tab-pane fade show active"
                    id="empty"
                    role="tabpanel"
                    aria-labelledby="empty-tab"
                >
                    <div class="card overflow-hidden">
                        <div class="card-body card-bg-secondary ">
                            <div><h5>Currently, there are no roles or permissions.</h5></div>
                        </div>
                    </div>
                </div>
                @endcan
            </div>
        </div>

        {{-- Edit Permission Modal --}}
        <div
            class="modal fade"
            id="editPermissionModal"
            tabindex="-1"
            aria-labelledby="editPermissionModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <form id="editPermissionForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editPermissionModalLabel">
                                Edit Permission
                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>

                        <div class="modal-body">
                            <label for="edit_permission_name" class="form-label">
                                Permission Key
                            </label>

                            <input
                                type="text"
                                id="edit_permission_name"
                                name="name"
                                class="form-control"
                                required
                            >

                            <small class="text-muted">
                                Renaming a permission may affect existing role assignments.
                            </small>
                        </div>

                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                            >
                                Cancel
                            </button>

                            <button type="submit" class="btn btn-primary">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        </div>
@endsection


@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#permission-table').DataTable({
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
                url: "{{ route('admin.role-permission.permission-datatable') }}",
                type: "GET",
            },
            layout: {
                topStart: 'pageLength',
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: 'paging',
            },
            columnDefs: [
                { orderable: false, targets: [0] },
            ],
            columns: [
                { data: 'name', name: 'name', orderable: true, searchable: true, width: '100%', },
                { data: 'action', name: 'action', orderable: false, searchable: false},
            ],
        });

        $('table#permission-table').css('font-size', '15px');

        $(document).ready(function () {

            const modules = @json($modules);

            const $module = $('#module');
            const $action = $('#action');
            const $preview = $('#permission_preview');
            const $addButton = $('#addPermissionBtn');

            function updatePermissionPreview() {
                const module = $module.val();
                const action = $action.val();

                const permission = module && action
                    ? `${module}.${action}`
                    : '';

                $preview.val(permission);
                $addButton.prop('disabled', !permission);
            }

            $module.on('change', function () {
                const module = $(this).val();

                $action.empty();

                if (!module || !modules[module]) {
                    $action.append(
                        new Option('Select module first', '')
                    );
                    $action.prop('disabled', true);
                    updatePermissionPreview();
                    return;
                }

                $action.append(new Option('Select action', ''));

                modules[module].actions.forEach(function (action) {
                    $action.append(
                        new Option(action.charAt(0).toUpperCase() + action.slice(1), action)
                    );
                });

                $action.prop('disabled', false);
                updatePermissionPreview();
            });

            $action.on('change', updatePermissionPreview);

            $('#permissionSearch').on('input', function () {
                const search = $(this).val().toLowerCase();

                $('#permissionTableBody tr').each(function () {
                    const text = $(this).text().toLowerCase();
                    $(this).toggle(text.includes(search));
                });
            });

            // Populate the edit modal.
            // $(document).on('click', '.edit-permission-btn', function () {
            //     const id = $(this).data('id');
            //     const name = $(this).data('name');

            //     $('#edit_permission_name').val(name);

            //     $('#editPermissionForm').attr(
            //         'action',
            //         `/admin/role-permission/${id}`
            //     );
            // });


            $(document).on('click', '.btn-del-perm', function(e) {
                e.preventDefault();
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.role-permission.delete-permission', ':id') }}".replace(':id', id),
                            method: 'DELETE',
                            dataType: 'json',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                Swal.fire(
                                    'Deleted!',
                                    response.message || 'Permission has been deleted.',
                                    'success'
                                );
                                $('#permission-table').DataTable().ajax.reload();
                            },
                            error: function () {
                                Swal.fire(
                                    'Error!',
                                    'Unable to delete the permission.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        })
    });



</script>
@endsection