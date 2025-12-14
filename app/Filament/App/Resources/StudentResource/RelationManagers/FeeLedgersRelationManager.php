<?php

namespace App\Filament\App\Resources\StudentResource\RelationManagers;

use App\Models\StudentFeeLedger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class FeeLedgersRelationManager extends RelationManager
{
    protected static string $relationship = 'feeLedgers';

    protected static ?string $title = 'Fee Ledger (Yearly)';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('academic_year')
                ->helperText('Example: 2025-2026')
                ->required()
                ->maxLength(15),
            Forms\Components\TextInput::make('annual_fee_total')
                ->label('Whole Year Fee')
                ->numeric()
                ->prefix('₹')
                ->required()
                ->default(0),
            Forms\Components\TextInput::make('opening_balance')
                ->label('Previous Year Balance (Opening)')
                ->numeric()
                ->prefix('₹')
                ->required()
                ->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('academic_year')
            ->columns([
                Tables\Columns\TextColumn::make('academic_year')->searchable(),
                Tables\Columns\TextColumn::make('annual_fee_total')->money('INR')->label('Annual Fee'),
                Tables\Columns\TextColumn::make('opening_balance')->money('INR')->label('Opening'),
                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Grand Total')
                    ->money('INR')
                    ->state(fn (StudentFeeLedger $record) => $record->grand_total),
                Tables\Columns\TextColumn::make('total_paid')
                    ->label('Total Paid')
                    ->money('INR')
                    ->state(fn (StudentFeeLedger $record) => $record->total_paid),
                Tables\Columns\TextColumn::make('remaining_balance')
                    ->label('Remaining')
                    ->money('INR')
                    ->state(fn (StudentFeeLedger $record) => $record->remaining_balance),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $tenant = \Filament\Facades\Filament::getTenant();
                        if ($tenant) {
                            $data['school_id'] = $tenant->id;
                        }
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}

