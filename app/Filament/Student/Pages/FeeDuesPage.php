<?php

namespace App\Filament\Student\Pages;

use App\Models\StudentFeeLedger;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class FeeDuesPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string $view = 'filament.student.pages.fee-dues';

    protected static ?string $navigationLabel = 'Fee Dues';

    protected static ?int $navigationSort = 1;

    protected function getTableQuery(): Builder
    {
        $user = auth()->user();
        $student = $user->userable;

        if (!$student) {
            return StudentFeeLedger::query()->whereRaw('1 = 0');
        }

        return StudentFeeLedger::query()
            ->where('student_id', $student->id)
            ->orderBy('academic_year', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('academic_year')
                ->label('Academic Year')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('opening_balance')
                ->label('Prev. Year Balance')
                ->money('INR')
                ->sortable(),
            Tables\Columns\TextColumn::make('total_paid')
                ->label('Paid Till Now')
                ->money('INR')
                ->state(fn (StudentFeeLedger $record) => $record->total_paid),
            Tables\Columns\TextColumn::make('remaining_balance')
                ->label('Remaining')
                ->money('INR')
                ->state(fn (StudentFeeLedger $record) => $record->remaining_balance),
        ];
    }
}
