<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SystemSettingResource\Pages;
use App\Models\SystemSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SystemSettingResource extends Resource
{
    protected static ?string $model = SystemSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'System Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('pricing_tier_1')
                        ->label('Pricing Tier 1')
                        ->numeric()
                        ->prefix('₹'),
                    Forms\Components\TextInput::make('pricing_tier_2')
                        ->label('Pricing Tier 2')
                        ->numeric()
                        ->prefix('₹'),
                    Forms\Components\TextInput::make('trial_days')
                        ->numeric()
                        ->required()
                        ->default(7),
                    Forms\Components\FileUpload::make('payment_qr_code')
                        ->label('Payment QR Code')
                        ->image()
                        ->directory('payment-qr-codes')
                        ->visibility('public'),
                    Forms\Components\TextInput::make('payment_upi_id')
                        ->label('Payment UPI ID')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('support_email')
                        ->email()
                        ->label('Support Email'),
                    Forms\Components\TextInput::make('support_phone')
                        ->label('Support Phone'),
                    Forms\Components\Textarea::make('tutorial_video_url')
                        ->label('Tutorial Video URL')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pricing_tier_1')->money('INR'),
                Tables\Columns\TextColumn::make('pricing_tier_2')->money('INR'),
                Tables\Columns\TextColumn::make('trial_days'),
                Tables\Columns\TextColumn::make('support_email'),
                Tables\Columns\TextColumn::make('support_phone'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSystemSettings::route('/'),
        ];
    }
}
