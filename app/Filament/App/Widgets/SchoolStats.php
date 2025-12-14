<?php

namespace App\Filament\App\Widgets;

use App\Models\AttendanceRecord;
use App\Models\FeePayment;
use App\Models\SalaryPayment;
use App\Models\Student;
use App\Models\Staff;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\Auth;

class SchoolStats extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $tenant = \Filament\Facades\Filament::getTenant();

        if (!$tenant) {
            return [
                Card::make('Students', 0),
                Card::make('Staff', 0),
                Card::make('Fees This Month', '₹0.00'),
                Card::make('Salary This Month', '₹0.00'),
                Card::make('Attendance Today', 0),
            ];
        }

        $studentCount = Student::where('school_id', $tenant->id)->count();
        $staffCount = Staff::where('school_id', $tenant->id)->count();
        $feeThisMonth = FeePayment::where('school_id', $tenant->id)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount_paid');
        $salaryThisMonth = SalaryPayment::where('school_id', $tenant->id)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount_paid');
        $attendanceToday = AttendanceRecord::where('school_id', $tenant->id)
            ->whereDate('date', now()->toDateString())
            ->count();

        return [
            Card::make('Students', $studentCount),
            Card::make('Staff', $staffCount),
            Card::make('Fees This Month', '₹' . number_format($feeThisMonth, 2)),
            Card::make('Salary This Month', '₹' . number_format($salaryThisMonth, 2)),
            Card::make('Attendance Today', $attendanceToday),
        ];
    }
}
