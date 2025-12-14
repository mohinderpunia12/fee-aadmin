<?php

namespace App\Filament\Staff\Widgets;

use App\Models\AttendanceRecord;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class AttendanceStatsWidget extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $user = auth()->user();
        $staff = $user->userable;

        if (!$staff) {
            return [
                Card::make('Present Days', 0),
                Card::make('Absent Days', 0),
                Card::make('Late Days', 0),
            ];
        }

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $present = AttendanceRecord::where('attendanceable_type', \App\Models\Staff::class)
            ->where('attendanceable_id', $staff->id)
            ->where('status', 'present')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        $absent = AttendanceRecord::where('attendanceable_type', \App\Models\Staff::class)
            ->where('attendanceable_id', $staff->id)
            ->where('status', 'absent')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        $late = AttendanceRecord::where('attendanceable_type', \App\Models\Staff::class)
            ->where('attendanceable_id', $staff->id)
            ->where('status', 'late')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        return [
            Card::make('Present Days', $present),
            Card::make('Absent Days', $absent),
            Card::make('Late Days', $late),
        ];
    }
}
