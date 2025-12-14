<?php

namespace App\Filament\App\Resources\FeePaymentResource\Pages;

use App\Filament\App\Resources\FeePaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeePayment extends EditRecord
{
    protected static string $resource = FeePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
