<?php

namespace App\Filament\App\Resources\AttendanceRecordResource\Pages;

use App\Filament\App\Resources\AttendanceRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceRecords extends ListRecords
{
    protected static string $resource = AttendanceRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
