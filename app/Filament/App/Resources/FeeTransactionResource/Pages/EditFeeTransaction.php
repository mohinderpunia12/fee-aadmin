<?php

namespace App\Filament\App\Resources\FeeTransactionResource\Pages;

use App\Filament\App\Resources\FeeTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeeTransaction extends EditRecord
{
    protected static string $resource = FeeTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

