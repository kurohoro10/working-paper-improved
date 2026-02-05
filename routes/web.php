<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkingPaper\AttachmentController;
use App\Http\Controllers\WorkingPaper\ExpenseItemController;
use App\Http\Controllers\WorkingPaper\GuestAccessController;
use App\Http\Controllers\WorkingPaper\IncomeItemController;
use App\Http\Controllers\WorkingPaper\RentalPropertyController;
use App\Http\Controllers\WorkingPaper\WorkingPaperController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
// Home/Landing Page
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('working-papers.index');
    }
    return redirect()->route('login');
})->name('home');

// Guest Access Routes (no authentication required)
Route::prefix('working-paper')->name('working-papers.guest.')->group(function () {
    Route::get('{reference}/view', [GuestAccessController::class, 'view'])
        ->name('view');
    Route::post('request-new-token', [GuestAccessController::class, 'requestNewToken'])
        ->name('request-token');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return redirect()->route('working-papers.index');
    })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth.or.token')->group(function () {
    // Working Papers (Main Resource)
    Route::resource('working-papers', WorkingPaperController::class);

    // Additional Working Paper Routes
    Route::prefix('working-papers')->name('working-papers.')->group(function () {
        Route::post('{working_paper}/regenerate-token', [WorkingPaperController::class, 'regenerateToken'])
            ->name('regenerate-token');
        Route::get('{working_paper}/sections/{work_section}/quarterly-consolidation', [WorkingPaperController::class, 'quarterlyConsolidation'])
            ->name('quarterly-consolidation');
    });

    // Work Section Management
    Route::prefix('work-sections/{work_section}')->name('work-sections.')->group(function () {

        // Income Items
        Route::post('income', [IncomeItemController::class, 'store'])
            ->name('income.store');
        Route::put('income/{income}', [IncomeItemController::class, 'update'])
            ->name('income.update');
        Route::delete('income/{income}', [IncomeItemController::class, 'destroy'])
            ->name('income.destroy');

        // Expense Items
        Route::post('expenses', [ExpenseItemController::class, 'store'])
            ->name('expenses.store');
        Route::put('expenses/{expense}', [ExpenseItemController::class, 'update'])
            ->name('expenses.update');
        Route::delete('expenses/{expense}', [ExpenseItemController::class, 'destroy'])
            ->name('expenses.destroy');

        // Rental Properties
        Route::post('rental-properties', [RentalPropertyController::class, 'store'])
            ->name('rental-properties.store');
        Route::put('rental-properties/{rental_property}', [RentalPropertyController::class, 'update'])
            ->name('rental-properties.update');
        Route::delete('rental-properties/{rental_property}', [RentalPropertyController::class, 'destroy'])
            ->name('rental-properties.destroy');
    });

    // Attachment Management
    Route::prefix('attachments')->name('attachments.')->group(function () {
        Route::post('expenses/{expense}/upload', [AttachmentController::class, 'uploadForExpense'])
            ->name('upload-expense');
        Route::post('income/{income}/upload', [AttachmentController::class, 'uploadForIncome'])
            ->name('upload-income');
        Route::get('{attachment}/download', [AttachmentController::class, 'download'])
            ->name('download');
        Route::delete('{attachment}', [AttachmentController::class, 'destroy'])
            ->name('destroy');
    });
});

// Work Section API Routes
require __DIR__.'/api-worksections.php';

// Breeze Authentication Routes
require __DIR__.'/auth.php';
