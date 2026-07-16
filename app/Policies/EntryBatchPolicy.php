<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\EntryBatch;
use Illuminate\Auth\Access\HandlesAuthorization;

class EntryBatchPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EntryBatch');
    }

    public function view(AuthUser $authUser, EntryBatch $entryBatch): bool
    {
        return $authUser->can('View:EntryBatch');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EntryBatch');
    }

    public function update(AuthUser $authUser, EntryBatch $entryBatch): bool
    {
        return $authUser->can('Update:EntryBatch');
    }

    public function delete(AuthUser $authUser, EntryBatch $entryBatch): bool
    {
        return $authUser->can('Delete:EntryBatch');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EntryBatch');
    }

    public function restore(AuthUser $authUser, EntryBatch $entryBatch): bool
    {
        return $authUser->can('Restore:EntryBatch');
    }

    public function forceDelete(AuthUser $authUser, EntryBatch $entryBatch): bool
    {
        return $authUser->can('ForceDelete:EntryBatch');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EntryBatch');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EntryBatch');
    }

    public function replicate(AuthUser $authUser, EntryBatch $entryBatch): bool
    {
        return $authUser->can('Replicate:EntryBatch');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EntryBatch');
    }

}