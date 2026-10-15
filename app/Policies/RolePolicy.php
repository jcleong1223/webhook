<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('role.view-any');
    }

    public function view(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('role.view');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('role.create');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('role.update');
    }

    public function delete(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('role.delete');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('role.delete-any');
    }

    public function restore(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('role.restore');
    }

    public function forceDelete(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('role.force-delete');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('role.force-delete-any');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('role.restore-any');
    }

    public function replicate(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('role.replicate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('role.reorder');
    }
}
