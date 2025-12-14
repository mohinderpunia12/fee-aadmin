<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements HasTenants, FilamentUser, JWTSubject
{
    use HasFactory, Notifiable;

    public const ROLE_SUPERUSER = 'superuser';
    public const ROLE_SCHOOL_ADMIN = 'school_admin';
    public const ROLE_STAFF = 'staff';
    public const ROLE_STUDENT = 'student';

    protected $fillable = [
        'name',
        'email',
        'password',
        'school_id',
        'role',
        'userable_type',
        'userable_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function userable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPERUSER || $this->school_id === null;
    }

    public function isSchoolAdmin(): bool
    {
        return $this->role === self::ROLE_SCHOOL_ADMIN;
    }

    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function getTenants(Panel $panel): Collection
    {
        if ($this->isSuperAdmin()) {
            return School::all();
        }

        return School::where('id', $this->school_id)->get();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if (!$tenant instanceof School) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->school_id === $tenant->id;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $panelId = $panel->getId();

        // Admin panel - only superusers
        if ($panelId === 'admin') {
            return $this->isSuperAdmin();
        }

        // App panel - superusers and school admins
        if ($panelId === 'app') {
            return $this->isSuperAdmin() || $this->isSchoolAdmin();
        }

        // Staff panel - only staff members
        if ($panelId === 'staff') {
            return $this->isStaff();
        }

        // Student panel - only students
        if ($panelId === 'student') {
            return $this->isStudent();
        }

        return false;
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
