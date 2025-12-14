<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\VerifiesTenantOwnership;
use App\Models\SalaryPayment;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

class SalarySlipController extends Controller
{
    use VerifiesTenantOwnership;
    public function download($id)
    {
        $payment = SalaryPayment::with(['staff', 'salaryStructure', 'staff.school'])->findOrFail($id);
        
        $this->verifyTenantOwnership($payment);
        
        $school = $payment->staff->school;

        return Pdf::view('receipts.salary-slip', [
            'payment' => $payment,
            'school' => $school,
            'structure' => $payment->salaryStructure,
        ])
            ->name('salary-slip-' . $payment->id . '.pdf')
            ->download();
    }
}
