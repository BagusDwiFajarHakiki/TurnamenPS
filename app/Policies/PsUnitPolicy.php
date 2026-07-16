<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PsUnit;
use Illuminate\Auth\Access\HandlesAuthorization;

class PsUnitPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PsUnit');
    }

    public function view(AuthUser $authUser, PsUnit $psUnit): bool
    {
        return $authUser->can('View:PsUnit');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PsUnit');
    }

    public function update(AuthUser $authUser, PsUnit $psUnit): bool
    {
        return $authUser->can('Update:PsUnit');
    }

    public function delete(AuthUser $authUser, PsUnit $psUnit): bool
    {
        return $authUser->can('Delete:PsUnit');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PsUnit');
    }

    public function restore(AuthUser $authUser, PsUnit $psUnit): bool
    {
        return $authUser->can('Restore:PsUnit');
    }

    public function forceDelete(AuthUser $authUser, PsUnit $psUnit): bool
    {
        return $authUser->can('ForceDelete:PsUnit');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PsUnit');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PsUnit');
    }

    public function replicate(AuthUser $authUser, PsUnit $psUnit): bool
    {
        return $authUser->can('Replicate:PsUnit');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PsUnit');
    }

}