<?php

namespace App\Filament\App\Resources\FeePaymentResource\Pages;

use App\Filament\App\Resources\FeePaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeePayments extends ListRecords
{
    protected static string $resource = FeePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
