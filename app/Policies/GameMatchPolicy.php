<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\GameMatch;
use Illuminate\Auth\Access\HandlesAuthorization;

class GameMatchPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GameMatch');
    }

    public function view(AuthUser $authUser, GameMatch $gameMatch): bool
    {
        return $authUser->can('View:GameMatch');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GameMatch');
    }

    public function update(AuthUser $authUser, GameMatch $gameMatch): bool
    {
        return $authUser->can('Update:GameMatch');
    }

    public function delete(AuthUser $authUser, GameMatch $gameMatch): bool
    {
        return $authUser->can('Delete:GameMatch');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:GameMatch');
    }

    public function restore(AuthUser $authUser, GameMatch $gameMatch): bool
    {
        return $authUser->can('Restore:GameMatch');
    }

    public function forceDelete(AuthUser $authUser, GameMatch $gameMatch): bool
    {
        return $authUser->can('ForceDelete:GameMatch');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GameMatch');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GameMatch');
    }

    public function replicate(AuthUser $authUser, GameMatch $gameMatch): bool
    {
        return $authUser->can('Replicate:GameMatch');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GameMatch');
    }

}