<?php

namespace App\Models\Scopes;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Global scope that automatically filters models by the current tenant (school).
 * 
 * This scope ensures that all queries for tenant-scoped models are automatically
 * filtered by the current school_id, preventing cross-tenant data leakage.
 */
class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $tenant = $this->getCurrentTenant();

        if ($tenant) {
            $builder->where($model->getTable() . '.school_id', $tenant->id);
        }
    }

    /**
     * Get the current tenant from Filament context or authenticated user.
     *
     * @return \App\Models\School|null
     */
    protected function getCurrentTenant(): ?School
    {
        // Try to get tenant from Filament context first (App panel)
        if (class_exists(\Filament\Facades\Filament::class)) {
            try {
                $panel = \Filament\Facades\Filament::getCurrentPanel();
                
                // Only apply scope in App panel (tenant-aware), not Admin panel
                if ($panel && $panel->getId() === 'app') {
                    $tenant = \Filament\Facades\Filament::getTenant();
                    if ($tenant instanceof School) {
                        return $tenant;
                    }
                }
            } catch (\Exception $e) {
                // Filament context not available, fall through
            }
        }

        // Fallback for API routes: get tenant from authenticated user
        // Only if user is not a super admin
        $user = Auth::user();
        if ($user && $user->school_id && !$user->isSuperAdmin()) {
            return School::find($user->school_id);
        }

        // No tenant context available (Admin panel or unauthenticated)
        return null;
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function extend(Builder $builder): void
    {
        $builder->macro('withoutTenantScope', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });

        $builder->macro('forTenant', function (Builder $builder, ?School $tenant) {
            return $builder->withoutGlobalScope($this)->where($builder->getModel()->getTable() . '.school_id', $tenant?->id);
        });
    }
}

