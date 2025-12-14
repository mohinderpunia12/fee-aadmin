<?php

namespace App\Filament\App\Resources\FeePaymentResource\Pages;

use App\Filament\App\Resources\FeePaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFeePayment extends CreateRecord
{
    protected static string $resource = FeePaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['school_id'] = \Filament\Facades\Filament::getTenant()->id;

        return $data;
    }
}