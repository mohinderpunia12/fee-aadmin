<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\VerifiesTenantOwnership;
use App\Models\Staff;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

class StaffIdCardController extends Controller
{
    use VerifiesTenantOwnership;
    public function download($id)
    {
        $staff = Staff::with('school')->findOrFail($id);
        
        $this->verifyTenantOwnership($staff);
        
        $school = $staff->school;

        return Pdf::view('cards.staff-id', [
            'staff' => $staff,
            'school' => $school,
        ])
            ->name('staff-id-' . $staff->id . '.pdf')
            ->download();
    }
}
