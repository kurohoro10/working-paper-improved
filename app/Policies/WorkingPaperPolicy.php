<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkingPaper;

/**
 * Policy for working paper authorization
 *
 * Defines who can view, create, update, and delete working papers.
 */
class WorkingPaperPolicy
{
    /**
     * Determine if user can view any working papers
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can see the index
        // (filtering is done in the controller based on role)
        return true;
    }

    /**
     * Determine if user can view a specific working paper
     */
    public function view(User $user, WorkingPaper $workingPaper): bool
    {
        // Admin and internal staff can view any working paper
        if ($user->isInternal()) {
            return true;
        }

        // Clients can only view their own working papers
        return $user->id === $workingPaper->client_id;
    }

    /**
     * Determine if user can create working papers
     */
    public function create(User $user): bool
    {
        // Only admin and endurego_internal can create working papers
        return $user->canCreateWorkingPaper();
    }

    /**
     * Determine if user can update a working paper
     */
    public function update(User $user, WorkingPaper $workingPaper): bool
    {
        // Admin and internal staff can update any working paper
        if ($user->isInternal()) {
            return true;
        }

        // Clients can update their own working papers (for comments, etc.)
        return $user->id === $workingPaper->client_id;
    }

    /**
     * Determine if user can delete a working paper
     */
    public function delete(User $user, WorkingPaper $workingPaper): bool
    {
        // Only admin and internal staff can delete
        return $user->isInternal();
    }

    /**
     * Determine if user can regenerate access token
     */
    public function regenerateToken(User $user, WorkingPaper $workingPaper): bool
    {
        // Only internal staff can regenerate tokens
        return $user->isInternal();
    }
}
