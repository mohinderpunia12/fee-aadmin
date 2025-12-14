<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ClassroomResource\Pages;
use App\Filament\App\Resources\ClassroomResource\RelationManagers\StudentsRelationManager;
use App\Models\Classroom;
use App\Models\Staff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClassroomResource extends Resource
{
    protected static ?string $model = Classroom::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $tenantRelationshipName = 'classrooms';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Classroom')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('section')
                    ->label('Section')
                    ->maxLength(255),
                Forms\Components\Select::make('teacher_id')
                    ->label('Class Teacher')
                    ->relationship('teacher', 'name')
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Classroom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('section')
                    ->label('Section'),
                Tables\Columns\TextColumn::make('teacher.name')->label('Class Teacher'),
                Tables\Columns\TextColumn::make('students_count')->counts('students')->label('Students'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            StudentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassrooms::route('/'),
            'create' => Pages\CreateClassroom::route('/create'),
            'edit' => Pages\EditClassroom::route('/{record}/edit'),
        ];
    }
}
