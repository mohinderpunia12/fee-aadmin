<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\FeeTransactionResource\Pages;
use App\Models\FeeTransaction;
use App\Models\Student;
use App\Models\StudentFeeLedger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FeeTransactionResource extends Resource
{
    protected static ?string $model = FeeTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationLabel = 'Fee Transactions';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Placeholder::make('student_display')
                ->label('Student')
                ->content(fn (?FeeTransaction $record) => $record?->ledger?->student?->name ?? '-')
                ->visible(fn (?FeeTransaction $record) => (bool) $record),

            Forms\Components\Placeholder::make('academic_year_display')
                ->label('Academic Year')
                ->content(fn (?FeeTransaction $record) => $record?->ledger?->academic_year ?? '-')
                ->visible(fn (?FeeTransaction $record) => (bool) $record),

            Forms\Components\Select::make('student_id')
                ->label('Student')
                ->options(function () {
                    $tenant = \Filament\Facades\Filament::getTenant();
                    if (!$tenant) {
                        return [];
                    }
                    return Student::query()
                        ->where('school_id', $tenant->id)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray();
                })
                ->searchable()
                ->required()
                ->live()
                ->visible(fn (?FeeTransaction $record) => !$record),

            Forms\Components\Select::make('academic_year')
                ->label('Academic Year')
                ->options(function ($get) {
                    $studentId = $get('student_id');
                    if (!$studentId) {
                        return [];
                    }

                    return StudentFeeLedger::query()
                        ->where('student_id', $studentId)
                        ->orderBy('academic_year', 'desc')
                        ->pluck('academic_year', 'academic_year')
                        ->toArray();
                })
                ->required()
                ->live()
                ->helperText('Pick an existing ledger year; if none exists, create one under Student → Fee Ledger first.')
                ->visible(fn (?FeeTransaction $record) => !$record),

            Forms\Components\Hidden::make('ledger_id'),

            Forms\Components\TextInput::make('paid_amount')
                ->label('Paid Amount')
                ->numeric()
                ->prefix('₹')
                ->required(),

            Forms\Components\DatePicker::make('payment_date')
                ->required()
                ->default(now()),

            Forms\Components\TextInput::make('payment_method')
                ->maxLength(255),

            Forms\Components\TextInput::make('transaction_id')
                ->maxLength(255),

            Forms\Components\Textarea::make('note')
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ledger.student.name')
                    ->label('Student')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ledger.academic_year')
                    ->label('Year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('receipt_no')
                    ->label('Receipt #')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_method'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download Receipt')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => route('receipts.fee.transaction.download', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['ledger', 'ledger.student']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeTransactions::route('/'),
            'create' => Pages\CreateFeeTransaction::route('/create'),
            'edit' => Pages\EditFeeTransaction::route('/{record}/edit'),
        ];
    }
}

