<?php

namespace App\Filament\App\Resources\FeeTransactionResource\Pages;

use App\Filament\App\Resources\FeeTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeeTransactions extends ListRecords
{
    protected static string $resource = FeeTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

