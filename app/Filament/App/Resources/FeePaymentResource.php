<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\FeePaymentResource\Pages;
use App\Models\FeePayment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FeePaymentResource extends Resource
{
    protected static ?string $model = FeePayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Fee Payments';

    protected static ?string $modelLabel = 'Fee Payment';

    protected static ?string $pluralModelLabel = 'Fee Payments';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->label('Student')
                    ->relationship('student', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('fee_structure_id')
                    ->label('Fee Structure')
                    ->relationship('feeStructure', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\TextInput::make('amount_paid')
                    ->required()
                    ->numeric()
                    ->prefix('₹')
                    ->minValue(0)
                    ->step(0.01),
                Forms\Components\DatePicker::make('payment_date')
                    ->required()
                    ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('feeStructure.name')
                    ->label('Fee Structure')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount_paid')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('student_id')
                    ->label('Student')
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('payment_date')
                    ->form([
                        Forms\Components\DatePicker::make('paid_from')
                            ->label('Paid From'),
                        Forms\Components\DatePicker::make('paid_until')
                            ->label('Paid Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['paid_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date),
                            )
                            ->when(
                                $data['paid_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download Receipt')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => route('receipts.fee.download', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeePayments::route('/'),
            'create' => Pages\CreateFeePayment::route('/create'),
            'edit' => Pages\EditFeePayment::route('/{record}/edit'),
        ];
    }

    /**
     * Get the Eloquent query for the resource.
     * The global TenantScope will automatically filter by current tenant.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
