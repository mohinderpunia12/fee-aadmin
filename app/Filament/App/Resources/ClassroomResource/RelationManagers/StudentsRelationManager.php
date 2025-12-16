<?php

namespace App\Filament\App\Resources\ClassroomResource\RelationManagers;

use App\Models\AttendanceRecord;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';

    protected static ?string $title = 'Students';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('enrollment_no')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('enrollment_no')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('section')
                    ->searchable(),
                Tables\Columns\TextColumn::make('today_attendance')
                    ->label("Today's Status")
                    ->badge()
                    ->getStateUsing(function (Student $record) {
                        $tenant = \Filament\Facades\Filament::getTenant();
                        if (!$tenant) {
                            return 'N/A';
                        }
                        
                        $attendance = AttendanceRecord::where('school_id', $tenant->id)
                            ->where('attendanceable_type', Student::class)
                            ->where('attendanceable_id', $record->id)
                            ->where('date', now()->toDateString())
                            ->first();
                        
                        if (!$attendance) {
                            return 'Not Marked';
                        }
                        
                        return ucfirst($attendance->status);
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'Present' => 'success',
                            'Absent' => 'danger',
                            'Late' => 'warning',
                            default => 'gray',
                        };
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('takeAttendance')
                    ->label('Take Attendance')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->form([
                        Forms\Components\DatePicker::make('date')
                            ->label('Date')
                            ->default(now())
                            ->required()
                            ->helperText('Select the date for attendance'),
                        Forms\Components\Section::make('Students Attendance')
                            ->description('All students are marked as present by default. Uncheck students who are absent.')
                            ->schema([
                                Forms\Components\CheckboxList::make('present_students')
                                    ->label('Present Students')
                                    ->options(function () {
                                        $students = $this->getOwnerRecord()
                                            ->students()
                                            ->orderBy('name')
                                            ->get();
                                        
                                        $options = [];
                                        foreach ($students as $student) {
                                            $options[$student->id] = $student->name . ' (' . $student->enrollment_no . ')';
                                        }
                                        return $options;
                                    })
                                    ->default(function () {
                                        return $this->getOwnerRecord()
                                            ->students()
                                            ->pluck('id')
                                            ->toArray();
                                    })
                                    ->gridDirection('row')
                                    ->columns(2)
                                    ->searchable()
                                    ->descriptions(function () {
                                        $students = $this->getOwnerRecord()
                                            ->students()
                                            ->orderBy('name')
                                            ->get();
                                        
                                        $descriptions = [];
                                        foreach ($students as $student) {
                                            $classroomName = $student->classroom ? $student->classroom->name : 'No Classroom';
                                            $descriptions[$student->id] = 'Classroom: ' . $classroomName . ($student->section ? ' - Section: ' . $student->section : '');
                                        }
                                        return $descriptions;
                                    })
                                    ->helperText('Uncheck students who are absent. All checked students will be marked as present.')
                                    ->required(),
                            ]),
                    ])
                    ->action(function (array $data): void {
                        $classroom = $this->getOwnerRecord();
                        $tenant = \Filament\Facades\Filament::getTenant();
                        $date = $data['date'];
                        $presentStudentIds = $data['present_students'] ?? [];
                        
                        // Get all students in the classroom
                        $allStudents = $classroom->students()->get();
                        
                        $presentCount = 0;
                        $absentCount = 0;
                        
                        foreach ($allStudents as $student) {
                            $isPresent = in_array($student->id, $presentStudentIds);
                            
                            AttendanceRecord::updateOrCreate(
                                [
                                    'school_id' => $tenant->id,
                                    'attendanceable_type' => Student::class,
                                    'attendanceable_id' => $student->id,
                                    'date' => $date,
                                ],
                                [
                                    'status' => $isPresent ? 'present' : 'absent',
                                ]
                            );
                            
                            if ($isPresent) {
                                $presentCount++;
                            } else {
                                $absentCount++;
                            }
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Attendance recorded successfully')
                            ->body("Present: {$presentCount} | Absent: {$absentCount}")
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Take Attendance - ' . $this->getOwnerRecord()->name)
                    ->modalWidth('3xl')
                    ->modalSubmitActionLabel('Save Attendance'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
