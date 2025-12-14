<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class School extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'domain',
        'logo',
        'support_email',
        'support_phone',
        'support_address',
        'trial_ends_at',
        'subscription_status',
        'subscription_expires_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($school) {
            if (empty($school->slug)) {
                $school->slug = Str::slug($school->name);
            }
        });
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function feeStructures(): HasMany
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function feePayments(): HasMany
    {
        return $this->hasMany(FeePayment::class);
    }

    public function studentFeeLedgers(): HasMany
    {
        return $this->hasMany(StudentFeeLedger::class);
    }

    public function feeTransactions(): HasMany
    {
        return $this->hasMany(FeeTransaction::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function salaryStructures(): HasMany
    {
        return $this->hasMany(SalaryStructure::class);
    }

    public function salaryPayments(): HasMany
    {
        return $this->hasMany(SalaryPayment::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isOnTrial(): bool
    {
        return $this->subscription_status === 'trial' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    public function hasActiveSubscription(): bool
    {
        if ($this->subscription_status === 'trial') {
            return $this->isOnTrial();
        }

        return $this->subscription_status === 'active' && 
               $this->subscription_expires_at && 
               $this->subscription_expires_at->isFuture();
    }

    public function isSubscriptionExpired(): bool
    {
        return !$this->hasActiveSubscription();
    }

    public function activateTrial(int $days = 7): void
    {
        $this->update([
            'trial_ends_at' => now()->addDays($days),
            'subscription_status' => 'trial',
        ]);
    }

    public function activateSubscription(\DateTimeInterface $expiresAt): void
    {
        $this->update([
            'subscription_status' => 'active',
            'subscription_expires_at' => $expiresAt,
        ]);
    }
}
