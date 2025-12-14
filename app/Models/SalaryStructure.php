<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryStructure extends Model
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
        'staff_id',
        'month',
        'year',
        'base_salary',
        'allowances',
        'deductions',
        'total_salary',
    ];

    protected $casts = [
        'allowances' => 'array',
        'deductions' => 'array',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalaryPayment::class);
    }
}
