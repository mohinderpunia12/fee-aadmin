<?php

namespace App\Filament\Student\Pages;

use App\Models\FeeTransaction;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class FeeReceiptsPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static string $view = 'filament.student.pages.fee-receipts';

    protected static ?string $navigationLabel = 'Fee Receipts';

    protected static ?int $navigationSort = 2;

    protected function getTableQuery(): Builder
    {
        $user = auth()->user();
        $student = $user->userable;

        if (!$student) {
            return FeeTransaction::query()->whereRaw('1 = 0');
        }

        $ledgerIds = $student->feeLedgers()->pluck('id')->toArray();

        return FeeTransaction::query()
            ->whereIn('ledger_id', $ledgerIds)
            ->with(['ledger'])
            ->orderBy('payment_date', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('payment_date')
                ->date()
                ->sortable()
                ->label('Date'),
            Tables\Columns\TextColumn::make('receipt_no')
                ->label('Receipt #')
                ->searchable(),
            Tables\Columns\TextColumn::make('ledger.academic_year')
                ->label('Year')
                ->sortable(),
            Tables\Columns\TextColumn::make('paid_amount')
                ->money('INR')
                ->sortable()
                ->label('Amount'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('download')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn ($record) => route('receipts.fee.transaction.download', $record->id))
                ->openUrlInNewTab(),
        ];
    }
}
