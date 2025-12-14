<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

/**
 * Policy for Student model authorization.
 * 
 * Ensures users can only access students from their own school,
 * unless they are super admins.
 */
class StudentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // TenantScope handles filtering
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Student $student): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $student->school_id === $user->school_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isSchoolAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Student $student): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $student->school_id === $user->school_id && 
               ($user->isSchoolAdmin() || $user->isStaff());
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Student $student): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $student->school_id === $user->school_id && $user->isSchoolAdmin();
    }
}

