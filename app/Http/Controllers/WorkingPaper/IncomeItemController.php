<?php

namespace App\Http\Controllers\WorkingPaper;

use App\Http\Controllers\Controller;
use App\Models\IncomeItem;
use App\Models\WorkSection;
use Illuminate\Http\Request;

/**
 * Controller for managing income items within work sections
 */
class IncomeItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a new income item
     */
    public function store(Request $request, WorkSection $workSection)
    {
        $this->authorize('update', $workSection->workingPaper);

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'quarter' => 'nullable|string|in:all,q1,q2,q3,q4',
            'client_comment' => 'nullable|string|max:1000',
            'own_comment' => 'nullable|string|max:1000',
        ]);

        $income = $workSection->incomeItems()->create($validated);

        if ($request->expectsJson()) {
            return response()->json($income->load('attachments'), 201);
        }

        return redirect()->back()->with('success', 'Income item added successfully!');
    }

    /**
     * Update an existing income item
     */
    public function update(Request $request, IncomeItem $income)
    {
        $this->authorize('update', $income->workSection->workingPaper);

        $validated = $request->validate([
            'description' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'quarter' => 'nullable|string|in:all,q1,q2,q3,q4',
            'client_comment' => 'nullable|string|max:1000',
            'own_comment' => 'nullable|string|max:1000',
        ]);

        $income->update($validated);

        if ($request->expectsJson()) {
            return response()->json($income->fresh('attachments'));
        }

        return redirect()->back()->with('success', 'Income item updated successfully!');
    }

    /**
     * Delete an income item
     */
    public function destroy(Request $request, IncomeItem $income)
    {
        $this->authorize('update', $income->workSection->workingPaper);

        $income->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Income deleted successfully']);
        }

        return redirect()->back()->with('success', 'Income item deleted successfully!');
    }
}
