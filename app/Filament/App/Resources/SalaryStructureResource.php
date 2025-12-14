<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\SalaryStructureResource\Pages;
use App\Models\SalaryStructure;
use App\Models\Staff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SalaryStructureResource extends Resource
{
    protected static ?string $model = SalaryStructure::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('staff_id')
                    ->label('Staff')
                    ->options(fn () => Staff::query()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('month')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('year')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('base_salary')
                    ->numeric()
                    ->required()
                    ->prefix('₹'),
                Forms\Components\KeyValue::make('allowances')
                    ->label('Allowances')
                    ->keyLabel('Type')
                    ->valueLabel('Amount'),
                Forms\Components\KeyValue::make('deductions')
                    ->label('Deductions')
                    ->keyLabel('Type')
                    ->valueLabel('Amount'),
                Forms\Components\TextInput::make('total_salary')
                    ->numeric()
                    ->required()
                    ->prefix('₹'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('staff.name')->label('Staff')->searchable(),
                Tables\Columns\TextColumn::make('month'),
                Tables\Columns\TextColumn::make('year'),
                Tables\Columns\TextColumn::make('base_salary')->money('INR'),
                Tables\Columns\TextColumn::make('total_salary')->money('INR'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalaryStructures::route('/'),
            'create' => Pages\CreateSalaryStructure::route('/create'),
            'edit' => Pages\EditSalaryStructure::route('/{record}/edit'),
        ];
    }
}
