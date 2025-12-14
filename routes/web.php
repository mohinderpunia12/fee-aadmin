<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/register-school', function () {
    return view('register-school');
})->name('app.register');

Route::post('/register-school', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'school_name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|alpha_dash|unique:schools,slug',
        'domain' => 'nullable|url|max:255',
        'logo' => 'nullable|image|max:2048',
        'admin_name' => 'required|string|max:255',
        'admin_email' => 'required|email|max:255|unique:users,email',
        'admin_password' => 'required|string|min:8|confirmed',
    ]);

    $logoPath = null;
    if ($request->hasFile('logo')) {
        $logoPath = $request->file('logo')->store('school-logos', 'public');
    }

    // Create school with 7-day trial
    $school = \App\Models\School::create([
        'name' => $validated['school_name'],
        'slug' => $validated['slug'] ?? \Illuminate\Support\Str::slug($validated['school_name']),
        'domain' => $validated['domain'] ?? null,
        'logo' => $logoPath,
    ]);

    // Activate 7-day trial
    $school->activateTrial(7);

    // Create admin user for the school
    $user = \App\Models\User::create([
        'name' => $validated['admin_name'],
        'email' => $validated['admin_email'],
        'password' => \Illuminate\Support\Facades\Hash::make($validated['admin_password']),
        'school_id' => $school->id,
    ]);

    // Login the user
    auth()->login($user);

    // Redirect to school dashboard
    return redirect('/app/' . $school->slug);
})->name('app.register.submit');

Route::middleware(['auth'])->group(function () {
    Route::get('/receipts/fee/{id}/download', [\App\Http\Controllers\FeeReceiptController::class, 'download'])
        ->name('receipts.fee.download');
    Route::get('/receipts/fee-transaction/{id}/download', [\App\Http\Controllers\FeeReceiptController::class, 'downloadTransaction'])
        ->name('receipts.fee.transaction.download');
    Route::get('/receipts/salary/{id}/download', [\App\Http\Controllers\SalarySlipController::class, 'download'])
        ->name('receipts.salary.download');
    Route::get('/cards/student/{id}/download', [\App\Http\Controllers\StudentIdCardController::class, 'download'])
        ->name('cards.student.download');
    Route::get('/cards/staff/{id}/download', [\App\Http\Controllers\StaffIdCardController::class, 'download'])
        ->name('cards.staff.download');
});
