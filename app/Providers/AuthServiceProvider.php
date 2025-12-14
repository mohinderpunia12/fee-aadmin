<?php

namespace App\Providers;

use App\Models\FeePayment;
use App\Models\Staff;
use App\Models\Student;
use App\Policies\FeePaymentPolicy;
use App\Policies\StaffPolicy;
use App\Policies\StudentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * AuthServiceProvider registers authorization policies.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Student::class => StudentPolicy::class,
        Staff::class => StaffPolicy::class,
        FeePayment::class => FeePaymentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}

