<?php

namespace App\Filament\Staff\Pages;

use App\Models\AttendanceRecord;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class AttendanceHistoryPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string $view = 'filament.staff.pages.attendance-history';

    protected static ?string $navigationLabel = 'My Attendance';

    protected static ?int $navigationSort = 2;

    protected function getTableQuery(): Builder
    {
        $user = auth()->user();
        $staff = $user->userable;

        if (!$staff) {
            return AttendanceRecord::query()->whereRaw('1 = 0');
        }

        return AttendanceRecord::query()
            ->where('attendanceable_type', \App\Models\Staff::class)
            ->where('attendanceable_id', $staff->id)
            ->orderBy('date', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('date')
                ->date()
                ->sortable(),
            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'success' => 'present',
                    'danger' => 'absent',
                    'warning' => 'late',
                ]),
            Tables\Columns\TextColumn::make('check_in_time')
                ->time(),
            Tables\Columns\TextColumn::make('check_out_time')
                ->time(),
            Tables\Columns\TextColumn::make('notes')
                ->limit(50),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\Filter::make('date')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('from')
                        ->label('From Date'),
                    \Filament\Forms\Components\DatePicker::make('until')
                        ->label('Until Date'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                        )
                        ->when(
                            $data['until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                        );
                }),
        ];
    }

}
