@php
    $isEdit = ($mode ?? 'create') === 'edit';
    $formAction = $isEdit
        ? route('admin.role-permission.update-role', $role->id)
        : route('admin.role-permission.store-role');
    $roleName = old('name', $isEdit ? $role->name : '');
    // Permission keys already granted to this role — used to pre-check the list in edit mode.
    // old() keeps the user's selections when validation fails.
    $defaultSelected = $isEdit ? $role->permissions->pluck('name')->all() : [];
    $selectedPermissions = old('permissions', $defaultSelected);
@endphp

<div class="card-body card-bg-secondary">
    <div class="row g-3">

        {{-- Role details --}}
        <div class="col-md-4">
            <div class="info-box d-block p-3 h-100">
                <h6 class="mb-3">
                    <i class="fa-solid fa-address-book me-1"></i>
                    Role Details
                </h6>

                <form
                    id="roleForm"
                    method="POST"
                    action="{{ $formAction }}"
                >
                    @csrf
                    @if ($isEdit)
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Role Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control"
                            value="{{ $roleName }}"
                            placeholder="e.g. support-administrator"
                            required
                        >

                        <small class="text-muted">
                            A unique key, usually <code>module-manager</code>.
                        </small>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger py-2">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Selected permission keys are injected here before submit --}}
                    <div id="selected-permissions"></div>

                    <div class="d-flex justify-content-end">
                        <button
                            type="submit"
                            id="saveRoleBtn"
                            class="btn btn-primary text-light"
                            @if (! $roleName) disabled @endif
                        >
                            <i class="fa-solid fa-floppy-disk me-1 text-light"></i>
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Permission list --}}
        <div class="col-md-8">
            <div class="info-box d-block p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0">
                        <i class="fa-solid fa-list-check me-1"></i>
                        Permission List
                    </h6>

                    <span class="badge bg-secondary">
                        <span id="selected-count">{{ count($selectedPermissions) }}</span> / {{ $permissions->count() }} selected
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="permission-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 3rem;">
                                    <input
                                        type="checkbox"
                                        class="form-check-input border-light"
                                        id="permission-select-all"
                                        title="Select all"
                                    >
                                </th>
                                <th>Permission Key</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($permissions as $permission)
                                <tr>
                                    <td class="text-center">
                                        <input
                                            type="checkbox"
                                            class="form-check-input border-light permission-checkbox"
                                            value="{{ $permission->name }}"
                                            @if (in_array($permission->name, $selectedPermissions)) checked @endif
                                        >
                                    </td>
                                    <td>{{ $permission->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var $table = $('#permission-table');
        var allPermissionNames = @json($permissions->pluck('name')->values());
        // Seed the selection from the role's current permissions (empty on the create page).
        var selected = new Set(@json(array_values($selectedPermissions)));

        var $selectAll = $('#permission-select-all');
        var $form = $('#roleForm');
        var $name = $('#name');

        function updateUi() {
            $('#selected-count').text(selected.size);
            $('#saveRoleBtn').prop('disabled', !$name.val().trim());
        }

        function syncSelectAll() {
            var boxes = $table.find('input.permission-checkbox');
            var allChecked = boxes.length > 0 && boxes.toArray().every(function (box) {
                return box.checked;
            });
            $selectAll.prop('checked', allChecked);
        }

        $table.DataTable({
            paging: true,
            pageLength: 10,
            lengthChange: true,
            lengthMenu: [10, 25, 50, 100],
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: [0] },
            ],
            layout: {
                topStart: 'pageLength',
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: 'paging',
            },
            drawCallback: function () {
                // Re-apply the saved selection after every redraw (paging / search),
                // because DataTables rebuilds the rows and drops the DOM checked state.
                $table.find('input.permission-checkbox').each(function () {
                    this.checked = selected.has(String(this.value));
                });
                syncSelectAll();
            },
        });

        // Track individual checkbox changes (delegated, survives redraws).
        $table.on('change', 'input.permission-checkbox', function () {
            var value = String(this.value);

            if (this.checked) {
                selected.add(value);
            } else {
                selected.delete(value);
            }

            syncSelectAll();
            updateUi();
        });

        // Select / clear every permission across all pages.
        $selectAll.on('change', function () {
            var check = this.checked;

            allPermissionNames.forEach(function (name) {
                if (check) {
                    selected.add(String(name));
                } else {
                    selected.delete(String(name));
                }
            });

            $table.find('input.permission-checkbox').each(function () {
                this.checked = check;
            });

            updateUi();
        });

        $name.on('input', updateUi);

        // Inject the selected permission keys as hidden inputs right before submit,
        // since the checkboxes live outside the form and carry no name attribute.
        $form.on('submit', function () {
            var container = $('#selected-permissions').empty();

            selected.forEach(function (name) {
                container.append(
                    $('<input>').attr({ type: 'hidden', name: 'permissions[]', value: name })
                );
            });
        });

        updateUi();
    });
</script>
@endpush
