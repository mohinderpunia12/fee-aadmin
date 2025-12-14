<?php

namespace App\Filament\App\Resources\StudentResource\Pages;

use App\Filament\App\Resources\StudentResource;
use App\Models\ParentPaymentAmount;
use App\Models\Student;
use App\Models\StudentFeeLedger;
use App\Models\Classroom;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Validation\ValidationException;

class CreateStudentByParent extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = StudentResource::class;

    protected static string $view = 'filament.app.resources.student-resource.pages.create-student-by-parent';

    public function getHeading(): string
    {
        return 'Add Students by Parent';
    }

    public function getSubheading(): ?string
    {
        return 'Add multiple students under a parent. You can search for existing parents or create a new one.';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'parent_search' => '',
            'parent_name' => '',
            'parent_phone' => '',
            'parent_phone_secondary' => '',
            'children_count' => 1,
            'payment_amount' => 0,
            'ledger_academic_year' => now()->format('Y') . '-' . now()->addYear()->format('Y'),
            'children' => [
                [
                    'name' => '',
                    'enrollment_no' => '',
                    'email' => '',
                    'class' => '',
                    'section' => '',
                    'gender' => null,
                    'address' => '',
                    'classroom_id' => null,
                    'annual_fee' => 0,
                    'previous_year_balance' => 0,
                ],
            ],
        ]);
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Parent Information')
                ->schema([
                    Forms\Components\TextInput::make('parent_search')
                        ->label('Search Existing Parent')
                        ->helperText('Enter parent phone number or name to search for existing parent')
                        ->placeholder('Phone number or parent name')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $tenant = \Filament\Facades\Filament::getTenant();
                            if (!$tenant || empty(trim((string) $state))) {
                                return;
                            }

                            $search = trim((string) $state);
                            
                            // Try to find by phone first (more reliable)
                            $student = Student::where('school_id', $tenant->id)
                                ->where(function ($query) use ($search) {
                                    $query->where('parent_phone', $search)
                                        ->orWhere('parent_phone_secondary', $search);
                                })
                                ->first();

                            // If not found by phone, try by name
                            if (!$student) {
                                $student = Student::where('school_id', $tenant->id)
                                    ->where('parent_name', 'like', "%{$search}%")
                                    ->first();
                            }

                            if ($student) {
                                $set('parent_name', $student->parent_name ?? '');
                                $set('parent_phone', $student->parent_phone ?? '');
                                $set('parent_phone_secondary', $student->parent_phone_secondary ?? '');
                                
                                // Get parent payment amount if exists
                                $parentPayment = ParentPaymentAmount::where('school_id', $tenant->id)
                                    ->where('parent_phone', $student->parent_phone)
                                    ->first();
                                
                                if ($parentPayment) {
                                    $set('payment_amount', $parentPayment->payment_amount);
                                }
                            }
                        }),

                    Forms\Components\TextInput::make('parent_name')
                        ->label('Parent Name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('parent_phone')
                        ->label('Parent Phone')
                        ->tel()
                        ->required()
                        ->maxLength(255)
                        ->helperText('This will be used as unique identifier to find all children'),

                    Forms\Components\TextInput::make('parent_phone_secondary')
                        ->label('Secondary Parent Phone')
                        ->tel()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('payment_amount')
                        ->label('Parent Payment Amount (Total for all children)')
                        ->numeric()
                        ->prefix('₹')
                        ->default(0)
                        ->helperText('Total amount parent will pay for all children'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Academic Year')
                ->schema([
                    Forms\Components\TextInput::make('ledger_academic_year')
                        ->label('Academic Year')
                        ->helperText('Example: 2025-2026')
                        ->default(now()->format('Y') . '-' . now()->addYear()->format('Y'))
                        ->required(),
                ]),

            Forms\Components\Section::make('Children')
                ->schema([
                    Forms\Components\TextInput::make('children_count')
                        ->label('Number of Children')
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $count = (int) $state;
                            $currentChildren = $this->data['children'] ?? [];
                            $children = [];

                            for ($i = 0; $i < $count; $i++) {
                                $children[] = $currentChildren[$i] ?? [
                                    'name' => '',
                                    'enrollment_no' => '',
                                    'email' => '',
                                    'class' => '',
                                    'section' => '',
                                    'gender' => null,
                                    'address' => '',
                                    'classroom_id' => null,
                                    'annual_fee' => 0,
                                    'previous_year_balance' => 0,
                                ];
                            }

                            $set('children', $children);
                        }),

                    Forms\Components\Repeater::make('children')
                        ->label('Student Details')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Student Name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('enrollment_no')
                                ->label('Enrollment Number')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->maxLength(255),
                            Forms\Components\Select::make('classroom_id')
                                ->label('Classroom')
                                ->options(fn () => Classroom::query()->pluck('name', 'id'))
                                ->searchable(),
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
                                ->rows(2),
                            Forms\Components\TextInput::make('annual_fee')
                                ->label('Annual Fee')
                                ->numeric()
                                ->prefix('₹')
                                ->default(0),
                            Forms\Components\TextInput::make('previous_year_balance')
                                ->label('Previous Year Balance')
                                ->numeric()
                                ->prefix('₹')
                                ->default(0),
                        ])
                        ->columns(3)
                        ->defaultItems(1)
                        ->minItems(1)
                        ->reorderable(false),
                ]),
        ];
    }

    public function submit(): void
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        if (!$tenant) {
            Notification::make()->title('No school selected')->danger()->send();
            return;
        }

        $state = $this->form->getState();
        $parentName = trim((string) ($state['parent_name'] ?? ''));
        $parentPhone = trim((string) ($state['parent_phone'] ?? ''));
        $parentPhoneSecondary = trim((string) ($state['parent_phone_secondary'] ?? ''));
        $paymentAmount = (float) ($state['payment_amount'] ?? 0);
        $academicYear = trim((string) ($state['ledger_academic_year'] ?? ''));
        $children = $state['children'] ?? [];

        if (empty($parentPhone)) {
            throw ValidationException::withMessages(['data.parent_phone' => 'Parent phone is required.']);
        }

        if (empty($children)) {
            throw ValidationException::withMessages(['data.children' => 'At least one child is required.']);
        }

        // Check for duplicate enrollment numbers
        $enrollmentNos = array_filter(array_column($children, 'enrollment_no'));
        if (count($enrollmentNos) !== count(array_unique($enrollmentNos))) {
            throw ValidationException::withMessages(['data.children' => 'Enrollment numbers must be unique.']);
        }

        // Check if enrollment numbers already exist
        foreach ($enrollmentNos as $enrollmentNo) {
            $exists = Student::where('school_id', $tenant->id)
                ->where('enrollment_no', $enrollmentNo)
                ->exists();
            if ($exists) {
                throw ValidationException::withMessages(['data.children' => "Enrollment number {$enrollmentNo} already exists."]);
            }
        }

        // Create students
        $createdStudents = [];
        foreach ($children as $childData) {
            if (empty($childData['name']) || empty($childData['enrollment_no'])) {
                continue;
            }

            $student = Student::create([
                'school_id' => $tenant->id,
                'name' => $childData['name'],
                'email' => $childData['email'] ?? null,
                'enrollment_no' => $childData['enrollment_no'],
                'class' => $childData['class'],
                'section' => $childData['section'] ?? null,
                'gender' => $childData['gender'] ?? null,
                'address' => $childData['address'] ?? null,
                'classroom_id' => $childData['classroom_id'] ?? null,
                'parent_name' => $parentName,
                'parent_phone' => $parentPhone,
                'parent_phone_secondary' => !empty($parentPhoneSecondary) ? $parentPhoneSecondary : null,
            ]);

            // Create ledger
            if ($academicYear) {
                StudentFeeLedger::create([
                    'school_id' => $tenant->id,
                    'student_id' => $student->id,
                    'academic_year' => $academicYear,
                    'annual_fee_total' => (float) ($childData['annual_fee'] ?? 0),
                    'opening_balance' => (float) ($childData['previous_year_balance'] ?? 0),
                ]);
            }

            $createdStudents[] = $student;
        }

        // Store parent payment amount
        if ($paymentAmount > 0) {
            ParentPaymentAmount::updateOrCreate(
                [
                    'school_id' => $tenant->id,
                    'parent_phone' => $parentPhone,
                ],
                [
                    'parent_name' => $parentName,
                    'payment_amount' => $paymentAmount,
                ]
            );
        }

        Notification::make()
            ->title('Students created successfully')
            ->body(count($createdStudents) . ' student(s) added under parent ' . $parentName . ' (' . $parentPhone . ')')
            ->success()
            ->send();

        // Redirect to students list
        $this->redirect(StudentResource::getUrl('index'));
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('cancel')
                ->label('Cancel')
                ->url(StudentResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}

