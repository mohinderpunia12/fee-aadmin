<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\VerifiesTenantOwnership;
use App\Models\FeePayment;
use App\Models\FeeTransaction;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

class FeeReceiptController extends Controller
{
    use VerifiesTenantOwnership;
    public function download($id)
    {
        $payment = FeePayment::with(['student', 'feeStructure', 'student.school'])->findOrFail($id);
        
        $this->verifyTenantOwnership($payment);
        
        $school = $payment->student->school;

        return Pdf::view('receipts.fee-receipt', [
            'payment' => $payment,
            'school' => $school,
        ])
            ->name('fee-receipt-' . $payment->id . '.pdf')
            ->download();
    }

    public function downloadTransaction($id)
    {
        $transaction = FeeTransaction::with(['ledger', 'ledger.student', 'ledger.student.school'])
            ->findOrFail($id);

        $this->verifyTenantOwnership($transaction);

        $ledger = $transaction->ledger;
        $student = $ledger->student;
        $school = $student->school;

        $grandTotal = (float) $ledger->annual_fee_total + (float) $ledger->opening_balance;
        $totalPaid = (float) $ledger->transactions()->sum('paid_amount');
        $remaining = max(0, $grandTotal - $totalPaid);

        return Pdf::view('receipts.fee-transaction-receipt', [
            'transaction' => $transaction,
            'ledger' => $ledger,
            'student' => $student,
            'school' => $school,
            'grandTotal' => $grandTotal,
            'totalPaid' => $totalPaid,
            'remaining' => $remaining,
        ])
            ->name('fee-receipt-' . ($transaction->receipt_no ?: $transaction->id) . '.pdf')
            ->download();
    }
}
