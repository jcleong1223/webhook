<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Session;

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

        return view($this->folder . 'index', compact('modules', 'permissionCount'));
    }

    public function permissionDatatable()
    {
        $permission = Permission::query();

        return DataTables::of($permission)
            ->addColumn('action', function ($permission) {
                return sprintf(
                    '<a href="' . route('admin.user.index', $permission->id) . '" style="font-size: 12px; cursor: pointer; justify-self: center;" class="btn-sm btn-outline-danger" ><i class="fa fa-trash" aria-hidden="true"></i></a>',
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
}
