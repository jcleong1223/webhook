<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionController extends Controller
{
    //
    protected $folder = '';

    public function __construct()
    {
        $this->folder = 'admin.role.';
    }

    public function index()
    {
        $modules = config('permission.modules');
        $permissionCount = Permission::all()->count();

        $roles = Role::all();

        return view($this->folder . 'index', compact('modules', 'permissionCount', 'roles'));
    }

    public function permissionDatatable(Request $request)
    {
        $permission = Permission::query()
                    ->when($request->role_id, function ($query) use ($request) {
                        $query->whereHas('roles', function ($roleQuery) use ($request) {
                            $roleQuery->where('id', $request->role_id);
                        });
                    });

        return DataTables::of($permission)
            ->addIndexColumn()
            ->editColumn('created_at', function ($permission) {

                $createdAt = $permission->created_at ? $permission->created_at->format('Y-m-d H:i:s') : '-';

                return $createdAt;
            })
            ->addColumn('action', function ($permission) {
                return sprintf(
                    '<a style="font-size: 12px; cursor: pointer; justify-self: center;" class="btn-sm btn-outline-danger btn-del-perm" data-id="%s"><i class="fa fa-trash" aria-hidden="true"></i></a>',
                    $permission->id
                );
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function storePermission(Request $request)
    {
        $validated = $request->validate([
            'module' => [
                'required',
                Rule::in(array_keys(config('permission.modules'))),
            ],
            'action' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $module = $request->input('module');
                    $modules = config('permission.modules');

                    // Check if module exists and if the action is allowed for this module
                    if (!isset($modules[$module]) || !in_array($value, $modules[$module]['actions'])) {
                        $fail("The selected action is invalid for the chosen module.");
                    }
                },
            ],
        ]);

        $permissionName = $validated['module'] . '.' . $validated['action'];

        $permission = Permission::firstOrCreate([
            'name' => $permissionName,
            'guard_name' => 'web',
        ]);

        Session::flash('success', 'Permission created successfully');
        return redirect()->back();
    }

    public function destroyPermission(int $id)
    {
        $permission = Permission::findOrFail($id);

        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission deleted successfully.',
        ]);
    }

    public function createRole(Request $request)
    {
        $permissions = Permission::all();

        return view($this->folder . 'create-role', compact('permissions'));
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        if (! empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Session::flash('success', 'Role created successfully.');

        return redirect()->route('admin.role-permission.index', ['tab' => 'role']);
    }

    public function showRole(int $id)
    {
        $role = Role::findOrFail($id);
        return view($this->folder . 'show-role', compact('role'));
    }

    public function editRole(int $id)
    {
        $role = Role::findOrFail($id);
        $role->load('permissions');
        $permissions = Permission::all();

        return view($this->folder . 'edit-role', compact('role', 'permissions'));
    }

    public function updateRole(Request $request, int $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Session::flash('success', 'Role updated successfully.');

        return redirect()->route('admin.role-permission.index', ['tab' => 'role']);
    }

}
