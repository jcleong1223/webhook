<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    //

    protected $folder = '';

    public function __construct()
    {
        $this->folder = 'admin.user.';
    }

    public function index()
    {
        return view($this->folder . 'index');
    }

    public function usersDatatable(Request $request)
    {
        $user = User::query()
                ->when($request->role_id, function ($query) use ($request) {
                    $role = Role::find($request->role_id);

                    if ($role) {
                        $query->role($role->name);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                });

        return DataTables::of($user)
            ->addIndexColumn()
            ->editColumn('role', function ($user) {

                return $user->roles->pluck('name')->implode(', ') ?? '-';
            })
            ->editColumn('status', function ($user) {
                switch ($user->status) {
                    case User::STATUS_ACTIVE:
                        return '<span class="badge badge-sm badge-success">Active</span>';
                    case User::STATUS_INACTIVE:
                        return '<span class="badge badge-sm badge-warning">Inactive</span>';
                    default:
                        return '<span class="badge badge-sm badge-info">N/A</span>';
                }
            })
            ->editColumn('created_at', function ($user) {

                $createdAt = $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '-';

                return $createdAt;
            })
            ->addColumn('action', function ($user) {
            return sprintf(
                '<a href="' . route('admin.user.edit', $user->id) . '" style="font-size: 12px; cursor: pointer" class="btn-sm btn-primary" ><i class="fa fa-edit" aria-hidden="true"></i></a>',
                $user->id
            );
            })
            ->rawColumns(['status', 'action'])
            ->make(true);


    }

    public function create()
    {
        $roles = Role::all();

        return view($this->folder . 'create', compact('roles'));
    }

    public function store(CreateUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'status' => $validated['status'],
        ]);

        $user->assignRole($validated['role']);

        Session::flash('success', 'User created successfully.');
        return redirect()->route('admin.user.index');
    }

    public function edit(int $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view($this->folder . 'edit', compact('user', 'roles'));
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'old_password' => 'nullable|required_with:password|string|min:10',
            'password' => 'nullable|required_with:old_password|string|min:10',
            'confirm_password' => 'nullable|string|same:password',
            'status' => 'required|string|in:active,inactive'
        ]);

        if (!empty($validated['password'])) {
            if (empty($validated['old_password'])) {
                Session::flash('error', 'Current password is required');

                return redirect()->back();
            }

            if (empty($validated['confirm_password'])) {
                Session::flash('error', 'Please confirm your new password');
                return redirect()->back();
            }

            if ($validated['old_password'] === $validated['password'] || Hash::check($validated['password'], $user->password)) {
                Session::flash('error', 'New password cannot be the same as the current password');
                return redirect()->back();
            }

            if (!Hash::check($validated['old_password'], $user->password)) {
                Session::flash('error', 'Current password is incorrect');
                return redirect()->back();
            }

            $updateData['password'] = Hash::make($validated['password']);
        }

        $updatedData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'status' => $validated['status'],
        ];

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'status' => $validated['status'],
        ]);

        Session::flash('success', 'User updated successfully.');
        return redirect()->route('admin.user.index');
    }
}
