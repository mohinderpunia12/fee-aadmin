<?php

namespace App\Filament\Admin\Resources\SystemSettingResource\Pages;

use App\Filament\Admin\Resources\SystemSettingResource;
use App\Models\SystemSetting;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSystemSettings extends ManageRecords
{
    protected static string $resource = SystemSettingResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data) {
                    return SystemSetting::instance()->fill($data)->tap(fn ($setting) => $setting->save());
                })
                ->label('Save Settings')
                ->createAnother(false),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }
}
