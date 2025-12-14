<?php

namespace App\Models\Traits;

use App\Models\School;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToSchool
{
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function scopeForSchool($query, ?School $school)
    {
        if ($school) {
            return $query->where('school_id', $school->id);
        }

        return $query;
    }
}
