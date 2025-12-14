<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AttendanceRecord extends Model
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
        'attendanceable_type',
        'attendanceable_id',
        'date',
        'status',
        'check_in_time',
        'check_out_time',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function attendanceable(): MorphTo
    {
        return $this->morphTo();
    }
}
