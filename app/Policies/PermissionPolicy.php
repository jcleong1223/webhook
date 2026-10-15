<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('permission.view-any');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('permission.view');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('permission.create');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('permission.update');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('permission.delete');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('permission.delete-any');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('permission.restore');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('permission.force-delete');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('permission.force-delete-any');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('permission.restore-any');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('permission.replicate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('permission.reorder');
    }
}
