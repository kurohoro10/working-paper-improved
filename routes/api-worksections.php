<?php

use App\Http\Controllers\WorkingPaper\WorkSectionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Work Section API Routes
|--------------------------------------------------------------------------
|
| These routes handle AJAX requests for managing work section data
| All routes require authentication
|
*/

Route::middleware(['auth'])->prefix('api/work-sections')->name('api.work-sections.')->group(function () {

    // Income Management
    Route::post('{section}/income', [WorkSectionController::class, 'storeIncome'])->name('income.store');
    Route::put('{section}/income/{income}', [WorkSectionController::class, 'updateIncome'])->name('income.update');
    Route::delete('{section}/income/{income}', [WorkSectionController::class, 'destroyIncome'])->name('income.destroy');
    Route::get('{section}/income', [WorkSectionController::class, 'getIncome'])->name('income.index');

    // Expense Management
    Route::post('{section}/expenses', [WorkSectionController::class, 'storeExpense'])->name('expenses.store');
    Route::put('{section}/expenses/{expense}', [WorkSectionController::class, 'updateExpense'])->name('expenses.update');
    Route::delete('{section}/expenses/{expense}', [WorkSectionController::class, 'destroyExpense'])->name('expenses.destroy');
    Route::get('{section}/expenses', [WorkSectionController::class, 'getExpenses'])->name('expenses.index');

    // File Uploads
    Route::post('income/{income}/upload', [WorkSectionController::class, 'uploadIncomeFile'])->name('income.upload');
    Route::post('expenses/{expense}/upload', [WorkSectionController::class, 'uploadExpenseFile'])->name('expenses.upload');

    // Attachment Management
    Route::delete('attachments/{attachment}', [WorkSectionController::class, 'deleteAttachment'])->name('attachments.delete');
    Route::get('attachments/{attachment}/download', [WorkSectionController::class, 'downloadAttachment'])->name('attachments.download');

    // Section Summary
    Route::get('{section}/summary', [WorkSectionController::class, 'getSummary'])->name('summary');
});
