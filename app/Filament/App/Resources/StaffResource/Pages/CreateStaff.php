<?php

namespace App\Filament\App\Resources\StaffResource\Pages;

use App\Filament\App\Resources\StaffResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['school_id'] = \Filament\Facades\Filament::getTenant()->id;

        return $data;
    }
}