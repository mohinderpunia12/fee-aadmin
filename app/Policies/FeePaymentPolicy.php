<?php

namespace App\Policies;

use App\Models\FeePayment;
use App\Models\User;

/**
 * Policy for FeePayment model authorization.
 * 
 * Ensures users can only access fee payments from their own school,
 * unless they are super admins.
 */
class FeePaymentPolicy
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
    public function view(User $user, FeePayment $feePayment): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $feePayment->school_id === $user->school_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isSchoolAdmin() || $user->isStaff();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FeePayment $feePayment): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $feePayment->school_id === $user->school_id && 
               ($user->isSchoolAdmin() || $user->isStaff());
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FeePayment $feePayment): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $feePayment->school_id === $user->school_id && $user->isSchoolAdmin();
    }
}

