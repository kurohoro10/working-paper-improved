<?php

namespace App\Http\Controllers\WorkingPaper;

use App\Http\Controllers\Controller;
use App\Models\ExpenseItem;
use App\Models\WorkSection;
use App\Services\GSTCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for managing expense items within work sections
 */
class ExpenseItemController extends Controller
{
    public function __construct(
        private GSTCalculationService $gstService
    ) {
        $this->middleware('auth');
    }

    /**
     * Store a new expense item
     */
    public function store(Request $request, WorkSection $workSection)
    {
        $this->authorize('update', $workSection->workingPaper);

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'field_type' => 'nullable|string|in:A,B,C',
            'quarter' => 'nullable|string|in:all,q1,q2,q3,q4',
            'amount_inc_gst' => 'required|numeric|min:0',
            'is_gst_free' => 'boolean',
            'client_comment' => 'nullable|string|max:1000',
            'own_comment' => 'nullable|string|max:1000',
        ]);

        /**
         * Calculate GST breakdown automatically
         * If is_gst_free is true, GST will be 0 and net will equal amount_inc_gst
         * Otherwise, calculate using standard 10% GST rate
         */
        $gstBreakdown = $this->gstService->calculateFromIncludingGst(
            $validated['amount_inc_gst'],
            $validated['is_gst_free'] ?? false
        );

        $expense = $workSection->expenseItems()->create(array_merge(
            $validated,
            $gstBreakdown
        ));

        if ($request->expectsJson()) {
            return response()->json($expense->load('attachments'), 201);
        }

        return redirect()->back()->with('success', 'Expense item added successfully!');
    }

    /**
     * Update an existing expense item
     */
    public function update(Request $request, ExpenseItem $expense)
    {
        $this->authorize('update', $expense->workSection->workingPaper);

        $validated = $request->validate([
            'description' => 'sometimes|required|string|max:255',
            'field_type' => 'nullable|string|in:A,B,C',
            'quarter' => 'nullable|string|in:all,q1,q2,q3,q4',
            'amount_inc_gst' => 'sometimes|required|numeric|min:0',
            'is_gst_free' => 'boolean',
            'client_comment' => 'nullable|string|max:1000',
            'own_comment' => 'nullable|string|max:1000',
        ]);

        /**
         * Recalculate GST if amount changed
         * This ensures GST breakdown is always accurate
         */
        if (isset($validated['amount_inc_gst'])) {
            $gstBreakdown = $this->gstService->calculateFromIncludingGst(
                $validated['amount_inc_gst'],
                $validated['is_gst_free'] ?? $expense->is_gst_free
            );
            $validated = array_merge($validated, $gstBreakdown);
        }

        $expense->update($validated);

        if ($request->expectsJson()) {
            return response()->json($expense->fresh('attachments'));
        }

        return redirect()->back()->with('success', 'Expense item updated successfully!');
    }

    /**
     * Delete an expense item
     */
    public function destroy(Request $request, ExpenseItem $expense)
    {
        $this->authorize('update', $expense->workSection->workingPaper);

        $expense->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Expense deleted successfully']);
        }

        return redirect()->back()->with('success', 'Expense item deleted successfully!');
    }
}
