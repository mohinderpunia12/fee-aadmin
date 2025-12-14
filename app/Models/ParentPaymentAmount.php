<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class ParentPaymentAmount extends Model
{
    use BelongsToSchool;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    protected $fillable = [
        'school_id',
        'parent_phone',
        'parent_name',
        'payment_amount',
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
    ];
}
