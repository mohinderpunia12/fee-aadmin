<?php

namespace App\Filament\App\Resources\StudentResource\Pages;

use App\Filament\App\Resources\StudentResource;
use App\Models\StudentFeeLedger;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected ?string $ledgerAcademicYear = null;
    protected float $annualFee = 0.0;
    protected float $previousYearBalance = 0.0;

    protected function afterFill(): void
    {
        $defaultAcademicYear = now()->format('Y') . '-' . now()->addYear()->format('Y');
        
        $ledger = StudentFeeLedger::where('student_id', $this->record->id)
            ->orderBy('academic_year', 'desc')
            ->first();

        if ($ledger) {
            $this->form->fill([
                'ledger_academic_year' => $ledger->academic_year,
                'annual_fee' => $ledger->annual_fee_total,
                'previous_year_balance' => $ledger->opening_balance,
            ]);
        } else {
            $this->form->fill([
                'ledger_academic_year' => $defaultAcademicYear,
                'annual_fee' => 0,
                'previous_year_balance' => 0,
            ]);
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->ledgerAcademicYear = (string) ($data['ledger_academic_year'] ?? '');
        $this->annualFee = (float) ($data['annual_fee'] ?? 0);
        $this->previousYearBalance = (float) ($data['previous_year_balance'] ?? 0);

        unset($data['ledger_academic_year'], $data['annual_fee'], $data['previous_year_balance']);

        return $data;
    }

    protected function afterSave(): void
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        $student = $this->record;

        if (!$tenant || !$student || !$this->ledgerAcademicYear) {
            return;
        }

        StudentFeeLedger::updateOrCreate(
            [
                'student_id' => $student->id,
                'academic_year' => $this->ledgerAcademicYear,
            ],
            [
                'school_id' => $tenant->id,
                'annual_fee_total' => $this->annualFee,
                'opening_balance' => $this->previousYearBalance,
            ],
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
