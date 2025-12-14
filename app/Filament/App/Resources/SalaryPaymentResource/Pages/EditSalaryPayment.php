<?php

namespace App\Filament\App\Resources\SalaryPaymentResource\Pages;

use App\Filament\App\Resources\SalaryPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalaryPayment extends EditRecord
{
    protected static string $resource = SalaryPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
