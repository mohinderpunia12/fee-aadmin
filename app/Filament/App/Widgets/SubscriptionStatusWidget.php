<?php

namespace App\Filament\App\Widgets;

use Filament\Widgets\Widget;

class SubscriptionStatusWidget extends Widget
{
    protected static string $view = 'filament.app.widgets.subscription-status';

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $tenant = \Filament\Facades\Filament::getTenant();

        return [
            'school' => $tenant,
            'isOnTrial' => $tenant?->isOnTrial() ?? false,
            'hasActiveSubscription' => $tenant?->hasActiveSubscription() ?? false,
            'trialEndsAt' => $tenant?->trial_ends_at,
            'subscriptionExpiresAt' => $tenant?->subscription_expires_at,
        ];
    }
}
