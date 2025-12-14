<?php

namespace App\Filament\App\Resources\FeeTransactionResource\Pages;

use App\Filament\App\Resources\FeeTransactionResource;
use App\Models\StudentFeeLedger;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateFeeTransaction extends CreateRecord
{
    protected static string $resource = FeeTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        if ($tenant) {
            $data['school_id'] = $tenant->id;
        }

        $studentId = $data['student_id'] ?? null;
        $academicYear = $data['academic_year'] ?? null;

        if (!$studentId || !$academicYear) {
            return $data;
        }

        $ledger = StudentFeeLedger::query()
            ->where('student_id', $studentId)
            ->where('academic_year', $academicYear)
            ->first();

        if (!$ledger) {
            throw ValidationException::withMessages([
                'academic_year' => 'No ledger found for this student/year. Create it under Student → Fee Ledger first.',
            ]);
        }

        $paidAmount = (float) ($data['paid_amount'] ?? 0);
        if ($paidAmount <= 0) {
            throw ValidationException::withMessages([
                'paid_amount' => 'Paid amount must be greater than 0.',
            ]);
        }

        if ($paidAmount > $ledger->remaining_balance) {
            throw ValidationException::withMessages([
                'paid_amount' => 'Paid amount cannot exceed remaining balance (₹' . number_format($ledger->remaining_balance, 2) . ').',
            ]);
        }

        $data['ledger_id'] = $ledger->id;

        if (empty($data['receipt_no'])) {
            // Simple sequential receipt number per school (timestamp-based fallback)
            $data['receipt_no'] = 'R-' . $tenant?->id . '-' . now()->format('YmdHis') . '-' . random_int(100, 999);
        }

        // These are helper fields (not DB columns on fee_transactions)
        unset($data['student_id'], $data['academic_year']);

        return $data;
    }
}

