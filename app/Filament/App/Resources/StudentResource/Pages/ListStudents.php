<?php

namespace App\Filament\App\Resources\StudentResource\Pages;

use App\Filament\App\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('createByParent')
                ->label('Add Students by Parent')
                ->icon('heroicon-o-user-group')
                ->url(StudentResource::getUrl('create-by-parent'))
                ->color('success'),
        ];
    }
}
