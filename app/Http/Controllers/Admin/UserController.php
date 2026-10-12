<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

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
        $user = User::query();

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
                '<a href="' . route('admin.user.edit', $user->id) . '" style="font-size: 12px; cursor: pointer" class="btn-sm btn-primary" ><i class="fa fa-eye" aria-hidden="true"></i></a>',
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

    public function store(Request $request)
    {
        return redirect()->route('admin.user.index');
    }

    public function edit(int $id)
    {
        return view($this->folder . 'edit');
    }

    public function rolesIndex()
    {
        return view($this->folder . 'roles');
    }
}
