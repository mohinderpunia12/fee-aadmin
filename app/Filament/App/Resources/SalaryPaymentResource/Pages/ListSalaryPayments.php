<?php

namespace App\Filament\App\Resources\SalaryPaymentResource\Pages;

use App\Filament\App\Resources\SalaryPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSalaryPayments extends ListRecords
{
    protected static string $resource = SalaryPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
