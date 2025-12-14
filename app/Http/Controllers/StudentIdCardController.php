<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\VerifiesTenantOwnership;
use App\Models\Student;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

class StudentIdCardController extends Controller
{
    use VerifiesTenantOwnership;
    public function download($id)
    {
        $student = Student::with(['school', 'classroom'])->findOrFail($id);
        
        $this->verifyTenantOwnership($student);
        
        $school = $student->school;

        return Pdf::view('cards.student-id', [
            'student' => $student,
            'school' => $school,
        ])
            ->name('student-id-' . $student->id . '.pdf')
            ->download();
    }
}
