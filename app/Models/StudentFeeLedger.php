<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentFeeLedger extends Model
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
        'student_id',
        'academic_year',
        'annual_fee_total',
        'opening_balance',
    ];

    protected $casts = [
        'annual_fee_total' => 'decimal:2',
        'opening_balance' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FeeTransaction::class, 'ledger_id');
    }

    public function getGrandTotalAttribute(): float
    {
        // We are only tracking previous-year balance as the payable target.
        // Annual fee is intentionally not used to keep the flow simple.
        return (float) $this->opening_balance;
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->transactions()->sum('paid_amount');
    }

    public function getRemainingBalanceAttribute(): float
    {
        return max(0, $this->grand_total - $this->total_paid);
    }
}

