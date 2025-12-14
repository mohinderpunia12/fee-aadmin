<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\StudentResource\Pages;
use App\Models\Classroom;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Students';

    protected static ?string $modelLabel = 'Student';

    protected static ?string $pluralModelLabel = 'Students';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('enrollment_no')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (\Illuminate\Validation\Rules\Unique $rule) {
                        return $rule->where('school_id', \Filament\Facades\Filament::getTenant()->id);
                    }),
                Forms\Components\Select::make('classroom_id')
                    ->label('Classroom')
                    ->options(fn () => Classroom::query()->pluck('name', 'id'))
                    ->searchable()
                    ->relationship('classroom', 'name'),
                Forms\Components\Select::make('gender')
                    ->label('Gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other',
                    ])
                    ->nullable(),
                Forms\Components\Textarea::make('address')
                    ->label('Address')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('profile_photo')
                    ->image()
                    ->directory('student-photos')
                    ->visibility('public'),
                Forms\Components\TextInput::make('parent_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('parent_phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('parent_phone_secondary')
                    ->label('Secondary Parent Phone')
                    ->tel()
                    ->maxLength(255),

                Forms\Components\Section::make('Fee Information')
                    ->description('Set annual fee and previous year balance (opening) for this student.')
                    ->schema([
                        Forms\Components\TextInput::make('ledger_academic_year')
                            ->label('Academic Year')
                            ->helperText('Example: 2025-2026')
                            ->default(now()->format('Y') . '-' . now()->addYear()->format('Y'))
                            ->required(),
                        Forms\Components\TextInput::make('annual_fee')
                            ->label('Annual Fee')
                            ->numeric()
                            ->prefix('₹')
                            ->default(0)
                            ->required(),
                        Forms\Components\TextInput::make('previous_year_balance')
                            ->label('Previous Year Balance (Opening)')
                            ->numeric()
                            ->prefix('₹')
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('enrollment_no')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('class')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('section')->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other',
                        default => 'N/A',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'male' => 'info',
                        'female' => 'success',
                        'other' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('classroom.name')->label('Classroom')->searchable(),
                Tables\Columns\TextColumn::make('parent_phone')->searchable(),
                Tables\Columns\TextColumn::make('parent_phone_secondary')->label('Secondary Phone')->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class')
                    ->options(function () {
                        return Student::query()
                            ->distinct()
                            ->pluck('class', 'class')
                            ->toArray();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('download_id')
                    ->label('Download ID Card')
                    ->icon('heroicon-o-identification')
                    ->url(fn ($record) => route('cards.student.download', $record->id))
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
            // Hidden to keep the fee flow simple from one screen.
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'create-by-parent' => Pages\CreateStudentByParent::route('/create-by-parent'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
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
