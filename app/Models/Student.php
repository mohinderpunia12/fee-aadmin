<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Student extends Model
{
    use BelongsToSchool, HasFactory;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    protected $fillable = [
        'school_id',
        'name',
        'email',
        'enrollment_no',
        'class',
        'section',
        'gender',
        'address',
        'parent_phone',
        'classroom_id',
        'profile_photo',
        'parent_name',
        'parent_phone_secondary',
    ];

    public function feePayments(): HasMany
    {
        return $this->hasMany(FeePayment::class);
    }

    public function feeLedgers(): HasMany
    {
        return $this->hasMany(StudentFeeLedger::class);
    }

    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }
}
