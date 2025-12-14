<?php

namespace App\Filament\App\Pages;

use App\Models\FeeTransaction;
use App\Models\ParentPaymentAmount;
use App\Models\Student;
use App\Models\StudentFeeLedger;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class Fees extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Fees';
    protected static ?string $navigationGroup = 'Fees';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.app.pages.fees';

    public ?array $data = [];
    public bool $showForm = false;

    public function mount(): void
    {
        $this->form->fill([
            'mode' => 'monthly',
            'payment_date' => now()->toDateString(),
            'academic_year' => now()->format('Y') . '-' . now()->addYear()->format('Y'),
            'parent_search' => null,
            'parent_phone' => null,
            'parent_name_display' => null,
            'parent_payment_amount' => null,
            'parent_custom_amount' => null,
        ]);
    }

    public function showAddForm(): void
    {
        $this->showForm = true;
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->form->fill([
            'mode' => 'monthly',
            'payment_date' => now()->toDateString(),
            'academic_year' => now()->format('Y') . '-' . now()->addYear()->format('Y'),
            'parent_search' => null,
            'parent_phone' => null,
            'parent_name_display' => null,
            'parent_payment_amount' => null,
            'parent_custom_amount' => null,
        ]);
    }

    protected function getTableQuery(): Builder
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        if (!$tenant) {
            return FeeTransaction::query()->whereRaw('1 = 0');
        }

        return FeeTransaction::query()
            ->where('school_id', $tenant->id)
            ->with(['ledger.student'])
            ->orderBy('payment_date', 'desc')
            ->orderBy('created_at', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('payment_date')
                ->date()
                ->sortable()
                ->label('Payment Date'),
            Tables\Columns\TextColumn::make('ledger.student.name')
                ->label('Student')
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query->whereHas('ledger.student', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
                }),
            Tables\Columns\TextColumn::make('ledger.student.enrollment_no')
                ->label('Enrollment No')
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query->whereHas('ledger.student', function ($q) use ($search) {
                        $q->where('enrollment_no', 'like', "%{$search}%");
                    });
                }),
            Tables\Columns\TextColumn::make('ledger.academic_year')
                ->label('Academic Year')
                ->sortable(),
            Tables\Columns\TextColumn::make('receipt_no')
                ->label('Receipt No')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('paid_amount')
                ->money('INR')
                ->sortable()
                ->label('Amount Paid'),
            Tables\Columns\TextColumn::make('payment_method')
                ->label('Payment Method')
                ->searchable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('download_receipt')
                ->label('Download Receipt')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn (FeeTransaction $record) => route('receipts.fee.transaction.download', $record->id))
                ->openUrlInNewTab(),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Tables\Actions\Action::make('add_payment')
                ->label('Add Fee Payment')
                ->icon('heroicon-o-plus')
                ->button()
                ->action('showAddForm'),
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Fee Payment')
                ->schema([
                    Forms\Components\Select::make('mode')
                        ->label('Fee Generation Type')
                        ->options([
                            'monthly' => 'Monthly Fee Generation',
                            'parent' => 'Fee by Parent',
                        ])
                        ->required()
                        ->live(),

                    Forms\Components\TextInput::make('academic_year')
                        ->label('Academic Year')
                        ->helperText('Used to find the student’s previous balance ledger (Example: 2025-2026).')
                        ->required()
                        ->live(onBlur: true),

                    Forms\Components\DatePicker::make('payment_date')
                        ->required(),
                ])
                ->columns(3),

            Forms\Components\Section::make('Monthly Fee Payment')
                ->visible(fn ($get) => $get('mode') === 'monthly')
                ->schema([
                    Forms\Components\Select::make('student_id')
                        ->label('Student')
                        ->options(function () {
                            $tenant = \Filament\Facades\Filament::getTenant();
                            if (!$tenant) {
                                return [];
                            }
                            return Student::query()
                                ->where('school_id', $tenant->id)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->searchable()
                        ->required(),

                    Forms\Components\Select::make('month')
                        ->label('Month')
                        ->options([
                            'January' => 'January',
                            'February' => 'February',
                            'March' => 'March',
                            'April' => 'April',
                            'May' => 'May',
                            'June' => 'June',
                            'July' => 'July',
                            'August' => 'August',
                            'September' => 'September',
                            'October' => 'October',
                            'November' => 'November',
                            'December' => 'December',
                        ])
                        ->required()
                        ->default(now()->format('F')),

                    Forms\Components\TextInput::make('paid_amount')
                        ->label('Pay Now')
                        ->numeric()
                        ->prefix('₹')
                        ->required(),
                ])
                ->columns(3),

            Forms\Components\Section::make('Parent Payment')
                ->visible(fn ($get) => $get('mode') === 'parent')
                ->schema([
                    Forms\Components\TextInput::make('parent_search')
                        ->label('Search by Parent Name or Phone')
                        ->helperText('Enter parent name or phone number. Phone is used as unique identifier.')
                        ->required(fn ($get) => $get('mode') === 'parent')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $tenant = \Filament\Facades\Filament::getTenant();
                            if (!$tenant) {
                                $set('parent_payment_amount', null);
                                $set('parent_phone', null);
                                $set('parent_name_display', null);
                                return;
                            }

                            $search = trim((string) $state);
                            if ($search === '') {
                                $set('parent_payment_amount', null);
                                $set('parent_phone', null);
                                $set('parent_name_display', null);
                                return;
                            }

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

                            if (!$student) {
                                $set('parent_payment_amount', null);
                                $set('parent_phone', null);
                                $set('parent_name_display', null);
                                return;
                            }

                            // Use parent_phone as the unique identifier
                            $parentPhone = $student->parent_phone;
                            $set('parent_phone', $parentPhone);

                            // Get parent payment amount
                            $parentPayment = ParentPaymentAmount::where('school_id', $tenant->id)
                                ->where('parent_phone', $parentPhone)
                                ->first();
                            
                            if ($parentPayment) {
                                $set('parent_payment_amount', $parentPayment->payment_amount);
                                $set('parent_name_display', $parentPayment->parent_name);
                            } else {
                                $set('parent_payment_amount', 0);
                                $set('parent_name_display', $student->parent_name);
                            }
                        }),

                    Forms\Components\Hidden::make('parent_phone'),

                    Forms\Components\TextInput::make('parent_name_display')
                        ->label('Parent Name')
                        ->disabled()
                        ->dehydrated(false)
                        ->visible(fn ($get) => !empty($get('parent_name_display'))),

                    Forms\Components\TextInput::make('parent_payment_amount')
                        ->label('Total Payment Amount (from registration)')
                        ->disabled()
                        ->dehydrated(false)
                        ->prefix('₹')
                        ->helperText('Total amount parent paid during student registration')
                        ->visible(fn ($get) => !empty($get('parent_payment_amount'))),

                    Forms\Components\TextInput::make('parent_custom_amount')
                        ->label('Custom Payment Amount')
                        ->numeric()
                        ->prefix('₹')
                        ->required(fn ($get) => $get('mode') === 'parent')
                        ->helperText('This payment will reduce the total amount paid during registration. This is not a monthly fee.')
                        ->minValue(0.01),
                ])
                ->columns(2),

            Forms\Components\Section::make('Payment Details')
                ->schema([
                    Forms\Components\TextInput::make('payment_method')->maxLength(255),
                    Forms\Components\TextInput::make('transaction_id')->maxLength(255),
                    Forms\Components\Textarea::make('note')->rows(3)->columnSpanFull(),
                ])
                ->columns(2),
        ];
    }

    public function submit(): void
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        if (!$tenant) {
            return;
        }

        $state = $this->form->getState();
        $mode = $state['mode'] ?? 'partial';
        $academicYear = trim((string) ($state['academic_year'] ?? ''));
        $paymentDate = $state['payment_date'] ?? now()->toDateString();

        if ($academicYear === '') {
            throw ValidationException::withMessages(['data.academic_year' => 'Academic year is required.']);
        }

        if ($mode === 'parent') {
            $parentPhone = trim((string) ($state['parent_phone'] ?? ''));
            if (empty($parentPhone)) {
                throw ValidationException::withMessages(['data.parent_search' => 'Parent not found. Please search by parent name or phone.']);
            }

            $customAmount = (float) ($state['parent_custom_amount'] ?? 0);
            if ($customAmount <= 0) {
                throw ValidationException::withMessages(['data.parent_custom_amount' => 'Custom payment amount must be greater than 0.']);
            }

            // Get or create parent payment record
            $parentPayment = ParentPaymentAmount::where('school_id', $tenant->id)
                ->where('parent_phone', $parentPhone)
                ->first();

            if (!$parentPayment) {
                // If no parent payment record exists, create one
                $student = Student::where('school_id', $tenant->id)
                    ->where('parent_phone', $parentPhone)
                    ->first();
                
                if (!$student) {
                    throw ValidationException::withMessages(['data.parent_search' => 'Parent not found.']);
                }

                $parentPayment = ParentPaymentAmount::create([
                    'school_id' => $tenant->id,
                    'parent_phone' => $parentPhone,
                    'parent_name' => $student->parent_name ?? '',
                    'payment_amount' => 0,
                ]);
            }

            // Check if custom amount exceeds available balance
            if ($customAmount > $parentPayment->payment_amount) {
                throw ValidationException::withMessages([
                    'data.parent_custom_amount' => 'Payment amount (₹' . number_format($customAmount, 2) . ') exceeds available balance (₹' . number_format($parentPayment->payment_amount, 2) . ').',
                ]);
            }

            // Reduce the parent payment amount
            $parentPayment->payment_amount = max(0, $parentPayment->payment_amount - $customAmount);
            $parentPayment->save();

            Notification::make()
                ->title('Parent payment recorded')
                ->body('Payment of ₹' . number_format($customAmount, 2) . ' has been deducted from parent\'s total payment amount.')
                ->success()
                ->send();
        } else {
            $studentId = (int) ($state['student_id'] ?? 0);
            $payAmount = (float) ($state['paid_amount'] ?? 0);
            if ($studentId <= 0) {
                throw ValidationException::withMessages(['data.student_id' => 'Student is required.']);
            }
            if ($payAmount <= 0) {
                throw ValidationException::withMessages(['data.paid_amount' => 'Pay amount must be greater than 0.']);
            }

            $student = Student::query()
                ->where('school_id', $tenant->id)
                ->where('id', $studentId)
                ->firstOrFail();

            $ledger = StudentFeeLedger::firstOrCreate(
                ['student_id' => $student->id, 'academic_year' => $academicYear],
                ['school_id' => $tenant->id, 'annual_fee_total' => 0, 'opening_balance' => 0],
            );

            if ($payAmount > $ledger->remaining_balance) {
                throw ValidationException::withMessages([
                    'data.paid_amount' => 'Amount exceeds remaining previous balance (₹' . number_format($ledger->remaining_balance, 2) . ').',
                ]);
            }

            $extraNote = '';
            if ($mode === 'monthly' && !empty($state['month'])) {
                $extraNote = "\nMonth: " . trim((string) $state['month']);
            }

            $transaction = FeeTransaction::create([
                'school_id' => $tenant->id,
                'ledger_id' => $ledger->id,
                'paid_amount' => $payAmount,
                'payment_date' => $paymentDate,
                'payment_method' => $state['payment_method'] ?? null,
                'transaction_id' => $state['transaction_id'] ?? null,
                'receipt_no' => 'R-' . $tenant->id . '-' . now()->format('YmdHis') . '-' . random_int(100, 999),
                'note' => trim(($state['note'] ?? '') . $extraNote),
            ]);

            Notification::make()
                ->title('Fee payment saved')
                ->body('Receipt No: ' . $transaction->receipt_no)
                ->success()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('download_receipt')
                        ->label('Download Receipt')
                        ->url(route('receipts.fee.transaction.download', $transaction->id))
                        ->openUrlInNewTab(),
                ])
                ->send();
        }

        // Reset form and hide it, then refresh table
        $this->showForm = false;
        $this->resetTable();
        $this->form->fill([
            'mode' => $state['mode'] ?? 'monthly',
            'payment_date' => now()->toDateString(),
            'academic_year' => now()->format('Y') . '-' . now()->addYear()->format('Y'),
            'student_id' => null,
            'paid_amount' => null,
            'month' => now()->format('F'),
            'payment_method' => null,
            'transaction_id' => null,
            'note' => null,
            'parent_search' => null,
            'parent_phone' => null,
            'parent_name_display' => null,
            'parent_payment_amount' => null,
            'parent_custom_amount' => null,
        ]);
    }
}

