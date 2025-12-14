<?php

namespace App\Filament\Admin\Resources\SchoolResource\Pages;

use App\Filament\Admin\Resources\SchoolResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditSchool extends EditRecord
{
    protected static string $resource = SchoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('activateSubscription')
                ->label('Activate Subscription')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('duration')
                        ->label('Duration (months)')
                        ->options([
                            1 => '1 Month',
                            3 => '3 Months',
                            6 => '6 Months',
                            12 => '12 Months',
                        ])
                        ->required()
                        ->default(1),
                ])
                ->action(function (array $data) {
                    $months = (int) $data['duration'];
                    $this->record->activateSubscription(now()->addMonths($months));
                    
                    Notification::make()
                        ->title('Subscription Activated')
                        ->success()
                        ->body("Subscription activated for {$months} month(s).")
                        ->send();
                    
                    $this->refreshFormData(['subscription_status', 'subscription_expires_at']);
                })
                ->visible(fn () => $this->record->subscription_status !== 'active' || $this->record->isSubscriptionExpired()),
            Actions\DeleteAction::make(),
        ];
    }
}