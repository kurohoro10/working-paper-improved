<?php

namespace App\Http\Controllers\WorkingPaper;

use App\Http\Controllers\Controller;
use App\Models\RentalProperty;
use App\Models\WorkSection;
use Illuminate\Http\Request;

/**
 * Controller for managing rental properties within work sections
 */
class RentalPropertyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a new rental property
     */
    public function store(Request $request, WorkSection $workSection)
    {
        $this->authorize('update', $workSection->workingPaper);

        // Verify this is a rental property work section
        if ($workSection->work_type->value !== 'rental_property') {
            return redirect()->back()->with('error', 'This work section does not support rental properties.');
        }

        $validated = $request->validate([
            'address_label' => 'required|string|max:255',
            'full_address' => 'nullable|string|max:500',
            'ownership_percentage' => 'nullable|numeric|min:0|max:100',
            'period_rented_from' => 'nullable|date',
            'period_rented_to' => 'nullable|date|after_or_equal:period_rented_from',
        ]);

        $property = $workSection->rentalProperties()->create($validated);

        if ($request->expectsJson()) {
            return response()->json($property, 201);
        }

        return redirect()->back()->with('success', 'Rental property added successfully!');
    }

    /**
     * Update an existing rental property
     */
    public function update(Request $request, RentalProperty $rentalProperty)
    {
        $this->authorize('update', $rentalProperty->workSection->workingPaper);

        $validated = $request->validate([
            'address_label' => 'sometimes|required|string|max:255',
            'full_address' => 'nullable|string|max:500',
            'ownership_percentage' => 'nullable|numeric|min:0|max:100',
            'period_rented_from' => 'nullable|date',
            'period_rented_to' => 'nullable|date|after_or_equal:period_rented_from',
        ]);

        $rentalProperty->update($validated);

        if ($request->expectsJson()) {
            return response()->json($rentalProperty->fresh());
        }

        return redirect()->back()->with('success', 'Rental property updated successfully!');
    }

    /**
     * Delete a rental property
     */
    public function destroy(Request $request, RentalProperty $rentalProperty)
    {
        $this->authorize('update', $rentalProperty->workSection->workingPaper);

        $rentalProperty->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Rental property deleted successfully']);
        }

        return redirect()->back()->with('success', 'Rental property deleted successfully!');
    }
}
