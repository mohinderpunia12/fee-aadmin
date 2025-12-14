<?php

namespace App\Filament\Admin\Widgets;

use App\Models\School;
use App\Models\SystemSetting;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalSchools = School::count();
        $activeSubscriptions = School::where('subscription_status', 'active')
            ->where('subscription_expires_at', '>', now())
            ->count();
        $trialSchools = School::where('subscription_status', 'trial')
            ->where('trial_ends_at', '>', now())
            ->count();
        
        // Calculate total earnings from active subscriptions
        $settings = SystemSetting::instance();
        $subscriptionPrice = $settings->pricing_tier_1 ?? 0; // Use tier 1 as default price
        
        // Calculate total earnings (assuming monthly subscription)
        // For simplicity, we'll calculate based on active subscriptions
        // You may want to track actual payments in a separate table
        $totalEarnings = $activeSubscriptions * $subscriptionPrice;

        return [
            Stat::make('Total Schools', $totalSchools)
                ->description('All registered schools')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),
            
            Stat::make('Active Subscriptions', $activeSubscriptions)
                ->description('Schools with active paid subscriptions')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('Trial Schools', $trialSchools)
                ->description('Schools currently on trial')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
            
            Stat::make('Total Earnings', '₹' . number_format($totalEarnings, 2))
                ->description('From active subscriptions (monthly)')
                ->descriptionIcon('heroicon-m-currency-rupee')
                ->color('success'),
        ];
    }
}
