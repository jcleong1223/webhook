<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApiClientPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('api-client.view-any');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('api-client.view');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('api-client.create');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('api-client.update');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('api-client.delete');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('api-client.delete-any');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('api-client.restore');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('api-client.force-delete');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('api-client.force-delete-any');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('api-client.restore-any');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('api-client.replicate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('api-client.reorder');
    }
}
