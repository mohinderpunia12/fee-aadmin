<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\SalaryPaymentResource\Pages;
use App\Models\SalaryPayment;
use App\Models\SalaryStructure;
use App\Models\Staff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SalaryPaymentResource extends Resource
{
    protected static ?string $model = SalaryPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('staff_id')
                    ->label('Staff')
                    ->options(fn () => Staff::query()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('salary_structure_id')
                    ->label('Salary Structure')
                    ->options(fn () => SalaryStructure::query()->pluck('id', 'id'))
                    ->searchable(),
                Forms\Components\TextInput::make('amount_paid')
                    ->numeric()
                    ->required()
                    ->prefix('₹'),
                Forms\Components\TextInput::make('advance_salary')
                    ->numeric()
                    ->default(0)
                    ->prefix('₹')
                    ->helperText('Advance amount already given/deducted for this salary payment.'),
                Forms\Components\DatePicker::make('payment_date')
                    ->required(),
                Forms\Components\TextInput::make('payment_method')
                    ->maxLength(255),
                Forms\Components\TextInput::make('transaction_id')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('staff.name')->label('Staff')->searchable(),
                Tables\Columns\TextColumn::make('salary_structure_id')->label('Structure'),
                Tables\Columns\TextColumn::make('amount_paid')->money('INR'),
                Tables\Columns\TextColumn::make('advance_salary')->money('INR')->label('Advance'),
                Tables\Columns\TextColumn::make('payment_date')->date(),
                Tables\Columns\TextColumn::make('payment_method'),
                Tables\Columns\TextColumn::make('transaction_id'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download Slip')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => route('receipts.salary.download', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalaryPayments::route('/'),
            'create' => Pages\CreateSalaryPayment::route('/create'),
            'edit' => Pages\EditSalaryPayment::route('/{record}/edit'),
        ];
    }
}
