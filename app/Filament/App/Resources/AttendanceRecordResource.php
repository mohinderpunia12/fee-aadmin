<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\AttendanceRecordResource\Pages;
use App\Models\AttendanceRecord;
use App\Models\Staff;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceRecordResource extends Resource
{
    protected static ?string $model = AttendanceRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('attendanceable_type')
                    ->label('Type')
                    ->options([
                        Student::class => 'Student',
                        Staff::class => 'Staff',
                    ])
                    ->required()
                    ->live(),
                Forms\Components\Select::make('attendanceable_id')
                    ->label('Person')
                    ->options(function ($get) {
                        return match ($get('attendanceable_type')) {
                            Student::class => Student::query()->pluck('name', 'id'),
                            Staff::class => Staff::query()->pluck('name', 'id'),
                            default => collect(),
                        };
                    })
                    ->required()
                    ->searchable(),
                Forms\Components\DatePicker::make('date')->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                    ])
                    ->default('present'),
                Forms\Components\TimePicker::make('check_in_time'),
                Forms\Components\TimePicker::make('check_out_time'),
                Forms\Components\Textarea::make('notes'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('attendanceable.name')->label('Person'),
                Tables\Columns\TextColumn::make('attendanceable_type')->label('Type')->formatStateUsing(fn ($state) => class_basename($state)),
                Tables\Columns\TextColumn::make('date')->date(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'present',
                        'danger' => 'absent',
                        'warning' => 'late',
                    ]),
                Tables\Columns\TextColumn::make('check_in_time'),
                Tables\Columns\TextColumn::make('check_out_time'),
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
            'index' => Pages\ListAttendanceRecords::route('/'),
            'create' => Pages\CreateAttendanceRecord::route('/create'),
            'edit' => Pages\EditAttendanceRecord::route('/{record}/edit'),
        ];
    }
}
