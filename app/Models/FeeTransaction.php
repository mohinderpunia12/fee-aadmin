<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeTransaction extends Model
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
        'ledger_id',
        'paid_amount',
        'payment_date',
        'payment_method',
        'transaction_id',
        'receipt_no',
        'note',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'paid_amount' => 'decimal:2',
    ];

    public function ledger(): BelongsTo
    {
        return $this->belongsTo(StudentFeeLedger::class, 'ledger_id');
    }
}

