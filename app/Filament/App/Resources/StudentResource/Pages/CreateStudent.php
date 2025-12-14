<?php

namespace App\Filament\App\Resources\StudentResource\Pages;

use App\Filament\App\Resources\StudentResource;
use App\Models\StudentFeeLedger;
use Filament\Resources\Pages\CreateRecord;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected ?string $ledgerAcademicYear = null;
    protected float $annualFee = 0.0;
    protected float $previousYearBalance = 0.0;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['school_id'] = \Filament\Facades\Filament::getTenant()->id;

        $this->ledgerAcademicYear = (string) ($data['ledger_academic_year'] ?? '');
        $this->annualFee = (float) ($data['annual_fee'] ?? 0);
        $this->previousYearBalance = (float) ($data['previous_year_balance'] ?? 0);

        unset($data['ledger_academic_year'], $data['annual_fee'], $data['previous_year_balance']);

        return $data;
    }

    protected function afterCreate(): void
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
}