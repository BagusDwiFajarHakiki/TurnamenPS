<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MatchDispute;
use Illuminate\Auth\Access\HandlesAuthorization;

class MatchDisputePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MatchDispute');
    }

    public function view(AuthUser $authUser, MatchDispute $matchDispute): bool
    {
        return $authUser->can('View:MatchDispute');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MatchDispute');
    }

    public function update(AuthUser $authUser, MatchDispute $matchDispute): bool
    {
        return $authUser->can('Update:MatchDispute');
    }

    public function delete(AuthUser $authUser, MatchDispute $matchDispute): bool
    {
        return $authUser->can('Delete:MatchDispute');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MatchDispute');
    }

    public function restore(AuthUser $authUser, MatchDispute $matchDispute): bool
    {
        return $authUser->can('Restore:MatchDispute');
    }

    public function forceDelete(AuthUser $authUser, MatchDispute $matchDispute): bool
    {
        return $authUser->can('ForceDelete:MatchDispute');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MatchDispute');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MatchDispute');
    }

    public function replicate(AuthUser $authUser, MatchDispute $matchDispute): bool
    {
        return $authUser->can('Replicate:MatchDispute');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MatchDispute');
    }

}