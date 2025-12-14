<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'grade',
        'section',
        'capacity',
        'teacher_id',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);

        // Auto-generate name from grade and section when saving
        static::saving(function ($classroom) {
            if ($classroom->grade && $classroom->section) {
                $classroom->name = $classroom->grade . ' - ' . $classroom->section;
            } elseif ($classroom->grade) {
                $classroom->name = $classroom->grade;
            }
        });
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
