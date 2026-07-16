<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlayerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Player');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('View:Player');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Player');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('Update:Player');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('Delete:Player');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Player');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('Restore:Player');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDelete:Player');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Player');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Player');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('Replicate:Player');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Player');
    }

}