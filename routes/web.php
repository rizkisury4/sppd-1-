<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Sppd\ExpenseController;
use App\Http\Controllers\Sppd\SppdRequestController;
use App\Http\Controllers\Sppd\AttachmentController;
use App\Http\Controllers\Sppd\ReportController;
use App\Http\Controllers\Admin\DepartmentController as AdminDepartmentController;
use App\Http\Controllers\Admin\TravelCategoryController as AdminTravelCategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeoController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('sppd/rekap', [ReportController::class, 'index'])->name('sppd.rekap');
    Route::resource('sppd', SppdRequestController::class);
    Route::post('sppd/{sppd}/ajukan', [SppdRequestController::class, 'ajukan'])->name('sppd.ajukan');
    Route::post('sppd/{sppd}/setujui', [SppdRequestController::class, 'setujui'])->name('sppd.setujui');
    Route::post('sppd/{sppd}/tolak', [SppdRequestController::class, 'tolak'])->name('sppd.tolak');
    Route::post('sppd/{sppd}/ajukan-ulang', [SppdRequestController::class, 'ajukanUlang'])->name('sppd.ajukanUlang');

    Route::get('/geo/cities', [GeoController::class, 'searchCity'])->name('geo.cities');

    Route::post('sppd/{sppd}/expenses', [ExpenseController::class, 'store'])->name('sppd.expenses.store');
    Route::put('sppd/{sppd}/expenses/{expense}', [ExpenseController::class, 'update'])->name('sppd.expenses.update');
    Route::delete('sppd/{sppd}/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('sppd.expenses.destroy');

    Route::post('sppd/{sppd}/attachments', [AttachmentController::class, 'store'])->name('sppd.attachments.store');
    Route::delete('sppd/{sppd}/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('sppd.attachments.destroy');

    

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('departments', AdminDepartmentController::class);
        Route::resource('travel-categories', AdminTravelCategoryController::class);
    });
});

require __DIR__.'/auth.php';
