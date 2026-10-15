<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('user.view-any');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('user.view');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('user.create');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('user.update');
    }

    public function updateAny(AuthUser $authUser): bool
    {
        return $authUser->can('user.update-any');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('user.delete');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('user.delete-any');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('user.restore');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('user.force-delete');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('user.force-delete-any');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('user.restore-any');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('user.replicate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('user.reorder');
    }
}
