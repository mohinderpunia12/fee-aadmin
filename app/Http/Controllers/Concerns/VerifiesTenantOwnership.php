<?php

namespace App\Http\Controllers\Concerns;

use App\Models\School;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait for controllers to verify tenant ownership of resources.
 * 
 * Provides a reusable method to check if the authenticated user
 * has access to a tenant-scoped resource.
 */
trait VerifiesTenantOwnership
{
    /**
     * Verify that the authenticated user has access to the resource's tenant.
     * 
     * @param  \Illuminate\Database\Eloquent\Model  $resource  The resource to check
     * @param  string  $schoolIdColumn  The column name for school_id (default: 'school_id')
     * @return void
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function verifyTenantOwnership(Model $resource, string $schoolIdColumn = 'school_id'): void
    {
        $user = auth()->user();

        // Super admins can access any resource
        if ($user && $user->isSuperAdmin()) {
            return;
        }

        // Check if resource belongs to user's school
        $resourceSchoolId = $resource->getAttribute($schoolIdColumn);
        
        if ($user && $resourceSchoolId !== $user->school_id) {
            abort(403, 'Unauthorized access to this resource.');
        }
    }
}

