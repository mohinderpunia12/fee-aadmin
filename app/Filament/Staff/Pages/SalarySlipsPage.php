<?php

namespace App\Filament\Staff\Pages;

use App\Models\SalaryPayment;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class SalarySlipsPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.staff.pages.salary-slips';

    protected static ?string $navigationLabel = 'My Salary Slips';

    protected static ?int $navigationSort = 1;

    protected function getTableQuery(): Builder
    {
        $user = auth()->user();
        $staff = $user->userable;

        if (!$staff) {
            return SalaryPayment::query()->whereRaw('1 = 0');
        }

        return SalaryPayment::query()
            ->where('staff_id', $staff->id)
            ->with(['salaryStructure', 'staff'])
            ->orderBy('payment_date', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('payment_date')
                ->date()
                ->sortable()
                ->label('Date'),
            Tables\Columns\TextColumn::make('amount_paid')
                ->money('INR')
                ->sortable()
                ->label('Amount'),
            Tables\Columns\TextColumn::make('advance_salary')
                ->money('INR')
                ->sortable()
                ->label('Advance'),
            Tables\Columns\TextColumn::make('payment_method')
                ->label('Method'),
            Tables\Columns\TextColumn::make('salaryStructure.month')
                ->label('Month')
                ->formatStateUsing(fn ($record) => $record->salaryStructure 
                    ? \Carbon\Carbon::create()->month($record->salaryStructure->month)->format('F') . ' ' . $record->salaryStructure->year
                    : '-'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('download')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn ($record) => route('receipts.salary.download', $record->id))
                ->openUrlInNewTab(),
        ];
    }
}
