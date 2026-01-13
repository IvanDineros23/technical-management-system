<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // Redirect based on role
    if ($user->role) {
        switch ($user->role->slug) {
            case 'marketing':
                return redirect()->route('marketing.dashboard');
            case 'tech_personnel':
                return redirect()->route('technician.dashboard');
            case 'tech_head':
                return redirect()->route('tech-head.dashboard');
            case 'signatory':
                return redirect()->route('signatory.dashboard');
            case 'accounting':
                return redirect()->route('accounting.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
        }
    }
    
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Marketing Routes
Route::middleware(['auth', 'verified'])->prefix('marketing')->name('marketing.')->group(function () {
    Route::get('/dashboard', function () {
        return view('marketing.dashboard');
    })->name('dashboard');
});

// Placeholder routes for other roles (to be implemented)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/technician/dashboard', function () {
        return 'Technician Dashboard - Coming Soon';
    })->name('technician.dashboard');
    
    Route::get('/tech-head/dashboard', function () {
        return 'Tech Head Dashboard - Coming Soon';
    })->name('tech-head.dashboard');
    
    Route::get('/signatory/dashboard', function () {
        return 'Signatory Dashboard - Coming Soon';
    })->name('signatory.dashboard');
    
    Route::get('/accounting/dashboard', function () {
        return 'Accounting Dashboard - Coming Soon';
    })->name('accounting.dashboard');
    
    Route::get('/admin/dashboard', function () {
        return 'Admin Dashboard - Coming Soon';
    })->name('admin.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
