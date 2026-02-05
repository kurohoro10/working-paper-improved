<?php

namespace App\Http\Controllers\WorkingPaper;

use App\Http\Controllers\Controller;
use App\Models\WorkSection;
use App\Models\IncomeItem;
use App\Models\ExpenseItem;
use App\Models\Attachment;
use App\Models\RentalProperty;
use App\Services\GSTCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WorkSectionController extends Controller
{
    protected $gstService;

    public function __construct(GSTCalculationService $gstService)
    {
        $this->gstService = $gstService;
    }

    /**
     * Store a new income item for a work section
     */
    public function storeIncome(Request $request, WorkSection $section)
    {
        $validated = $request->validate([
            'label'               => 'required|string|max:50',
            'description'         => 'required|string|max:255',
            'amount'              => 'required|numeric|min:0',
            'is_gst_free'         => 'boolean',
            'quarter'             => 'nullable|in:q1,q2,q3,q4,all',
            'rental_property_id'  => 'nullable|exists:rental_properties,id',
            'client_comment'      => 'nullable|string',
            'own_comment'         => 'nullable|string',
        ]);

        // If rental_property_id is provided, verify it belongs to this section
        if (isset($validated['rental_property_id'])) {
            $property = RentalProperty::find($validated['rental_property_id']);
            if (!$property || $property->work_section_id !== $section->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid rental property'
                ], 400);
            }
        }

        // Calculate GST for income
        $isGstFree = $validated['is_gst_free'] ?? false;
        $amount = $validated['amount'];

        if ($isGstFree) {
            $gstAmount = 0;
            $netAmount = $amount;
        } else {
            $gstAmount = $amount / 11;
            $netAmount = $amount - $gstAmount;
        }

        $income = $section->incomeItems()->create([
            'label'               => $validated['label'],
            'description'         => $validated['description'],
            'amount'              => $amount,
            'gst_amount'          => $gstAmount,
            'net_amount'          => $netAmount,
            'is_gst_free'         => $isGstFree,
            'quarter'             => $validated['quarter'] ?? 'all',
            'rental_property_id'  => $validated['rental_property_id'] ?? null,
            'client_comment'      => $validated['client_comment'] ?? null,
            'own_comment'         => $validated['own_comment'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Income item added successfully',
            'income'  => $income->load('attachments')
        ]);
    }

    /**
     * Update an income item
     */
    public function updateIncome(Request $request, WorkSection $section, IncomeItem $income)
    {
        // Verify income belongs to this section
        if ($income->work_section_id !== $section->id) {
            return response()->json([
                'success' => false,
                'message' => 'Income item does not belong to this section'
            ], 403);
        }

        $validated = $request->validate([
            'label'               => 'required|string|max:50',
            'description'         => 'required|string|max:255',
            'amount'              => 'required|numeric|min:0',
            'is_gst_free'         => 'boolean',
            'quarter'             => 'nullable|in:q1,q2,q3,q4,all',
            'rental_property_id'  => 'nullable|exists:rental_properties,id',
            'client_comment'      => 'nullable|string',
            'own_comment'         => 'nullable|string',
        ]);

        // Calculate GST for income
        $isGstFree = $validated['is_gst_free'] ?? $income->is_gst_free ?? false;
        $amount = $validated['amount'];

        if ($isGstFree) {
            $gstAmount = 0;
            $netAmount = $amount;
        } else {
            $gstAmount = $amount / 11;
            $netAmount = $amount - $gstAmount;
        }

        $income->update([
            'label'               => $validated['label'],
            'description'         => $validated['description'],
            'amount'              => $amount,
            'gst_amount'          => $gstAmount,
            'net_amount'          => $netAmount,
            'is_gst_free'         => $isGstFree,
            'quarter'             => $validated['quarter'] ?? $income->quarter,
            'rental_property_id'  => $validated['rental_property_id'] ?? $income->rental_property_id,
            'client_comment'      => $validated['client_comment'] ?? $income->client_comment,
            'own_comment'         => $validated['own_comment'] ?? $income->own_comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Income item updated successfully',
            'income' => $income->fresh()->load('attachments')
        ]);
    }

    /**
     * Delete an income item
     */
    public function destroyIncome(WorkSection $section, IncomeItem $income)
    {
        // Verify income belongs to this section
        if ($income->work_section_id !== $section->id) {
            return response()->json([
                'success' => false,
                'message' => 'Income item does not belong to this section'
            ], 403);
        }

        // Delete all attachments first
        foreach ($income->attachments as $attachment) {
            Storage::delete($attachment->file_path);
            $attachment->delete();
        }

        $income->delete();

        return response()->json([
            'success' => true,
            'message' => 'Income item deleted successfully'
        ]);
    }

    /**
     * Store a new expense item for a work section
     */
    public function storeExpense(Request $request, WorkSection $section)
    {
        $validated = $request->validate([
            'label'               => 'required|string|max:50',
            'description'         => 'required|string|max:255',
            'type'                => 'nullable|string|max:50',
            'field_type'          => 'nullable|in:A,B,C',
            'amount_inc_gst'      => 'required|numeric|min:0',
            'is_gst_free'         => 'boolean',
            'quarter'             => 'nullable|in:q1,q2,q3,q4,all',
            'rental_property_id'  => 'nullable|exists:rental_properties,id',
            'client_comment'      => 'nullable|string',
            'own_comment'         => 'nullable|string',
        ]);

        // If rental_property_id is provided, verify it belongs to this section
        if (isset($validated['rental_property_id'])) {
            $property = RentalProperty::find($validated['rental_property_id']);
            if (!$property || $property->work_section_id !== $section->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid rental property'
                ], 400);
            }
        }

        // Calculate GST automatically
        $isGstFree = $validated['is_gst_free'] ?? false;
        $gstCalculation = $this->gstService->calculateFromIncludingGst(
            $validated['amount_inc_gst'],
            $isGstFree
        );

        $expense = $section->expenseItems()->create([
            'label'               => $validated['label'],
            'description'         => $validated['description'],
            'type'                => $validated['type'] ?? null,
            'field_type'          => $validated['field_type'] ?? null,
            'amount_inc_gst'      => $gstCalculation['amount_inc_gst'],
            'gst_amount'          => $gstCalculation['gst_amount'],
            'net_ex_gst'          => $gstCalculation['net_ex_gst'],
            'is_gst_free'         => $isGstFree,
            'quarter'             => $validated['quarter'] ?? 'all',
            'rental_property_id'  => $validated['rental_property_id'] ?? null,
            'client_comment'      => $validated['client_comment'] ?? null,
            'own_comment'         => $validated['own_comment'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense item added successfully',
            'expense' => $expense->load('attachments')
        ]);
    }

    /**
     * Update an expense item
     */
    public function updateExpense(Request $request, WorkSection $section, ExpenseItem $expense)
    {
        dd($request);
        // Verify expense belongs to this section
        if ($expense->work_section_id !== $section->id) {
            return response()->json([
                'success' => false,
                'message' => 'Expense item does not belong to this section'
            ], 403);
        }

        $validated = $request->validate([
            'label'               => 'required|string|max:50',
            'description'         => 'required|string|max:255',
            'type'                => 'nullable|in:operating,admin,other',
            'field_type'          => 'nullable|in:A,B,C',
            'amount_inc_gst'      => 'required|numeric|min:0',
            'is_gst_free'         => 'boolean',
            'quarter'             => 'nullable|in:q1,q2,q3,q4,all',
            'rental_property_id'  => 'nullable|exists:rental_properties,id',
            'client_comment'      => 'nullable|string',
            'own_comment'         => 'nullable|string',
        ]);

        // Recalculate GST if amount changed
        $isGstFree = $validated['is_gst_free'] ?? $expense->is_gst_free;
        $gstCalculation = $this->gstService->calculateFromIncludingGst(
            $validated['amount_inc_gst'],
            $isGstFree
        );

        $expense->update([
            'label'               => $validated['label'],
            'description'         => $validated['description'],
            'type'                => $validated['type'] ?? $expense->type,
            'field_type'          => $validated['field_type'] ?? $expense->field_type,
            'amount_inc_gst'      => $gstCalculation['amount_inc_gst'],
            'gst_amount'          => $gstCalculation['gst_amount'],
            'net_ex_gst'          => $gstCalculation['net_ex_gst'],
            'is_gst_free'         => $isGstFree,
            'quarter'             => $validated['quarter'] ?? $expense->quarter,
            'rental_property_id'  => $validated['rental_property_id'] ?? $expense->rental_property_id,
            'client_comment'      => $validated['client_comment'] ?? $expense->client_comment,
            'own_comment'         => $validated['own_comment'] ?? $expense->own_comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense item updated successfully',
            'expense' => $expense->fresh()->load('attachments')
        ]);
    }

    /**
     * Delete an expense item
     */
    public function destroyExpense(WorkSection $section, ExpenseItem $expense)
    {
        // Verify expense belongs to this section
        if ($expense->work_section_id !== $section->id) {
            return response()->json([
                'success' => false,
                'message' => 'Expense item does not belong to this section'
            ], 403);
        }

        // Delete all attachments first
        foreach ($expense->attachments as $attachment) {
            Storage::delete($attachment->file_path);
            $attachment->delete();
        }

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense item deleted successfully'
        ]);
    }

    /**
     * Upload file for income item
     */
    public function uploadIncomeFile(Request $request, IncomeItem $income)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx'
        ]);

        try {
            $file = $request->file('file');

            // Generate unique filename
            $timestamp = now()->timestamp;
            $randomString = Str::random(8);
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $sanitizedName = Str::slug($originalName);
            $filename = "{$timestamp}_{$randomString}_{$sanitizedName}.{$extension}";

            // Store file
            $path = $file->storeAs(
                "working-papers/{$income->workSection->id}/income",
                $filename,
                'public'
            );



            // Create attachment record
            $attachment = $income->attachments()->create([
                'original_filename' => $file->getClientOriginalName(),
                'stored_filename'   => $filename,
                'file_path'         => $path,
                'mime_type'         => $file->getMimeType(),
                'file_size'         => $file->getSize(),
                'uploaded_by'       => auth()->id(),
            ]);

            return response()->json([
                'success'    => true,
                'message'    => 'File uploaded successfully',
                'attachment' => $attachment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'File upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload file for expense item
     */
    public function uploadExpenseFile(Request $request, ExpenseItem $expense)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx'
        ]);

        try {
            $file = $request->file('file');

            // Generate unique filename
            $timestamp = now()->timestamp;
            $randomString = Str::random(8);
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $sanitizedName = Str::slug($originalName);
            $filename = "{$timestamp}_{$randomString}_{$sanitizedName}.{$extension}";

            // Store file
            $path = $file->storeAs(
                "working-papers/{$expense->workSection->id}/expenses",
                $filename,
                'public'
            );

            // Create attachment record
            $attachment = $expense->attachments()->create([
                'original_filename' => $file->getClientOriginalName(),
                'stored_filename'   => $filename,
                'file_path'         => $path,
                'mime_type'         => $file->getMimeType(),
                'file_size'         => $file->getSize(),
                'uploaded_by'       => auth()->id(),
            ]);

            return response()->json([
                'success'    => true,
                'message'    => 'File uploaded successfully',
                'attachment' => $attachment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'File upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an attachment
     */
    public function deleteAttachment(Attachment $attachment)
    {
        try {
            // Delete physical file
            Storage::delete($attachment->file_path);

            // Delete database record
            $attachment->delete();

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'File deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download an attachment
     */
    public function downloadAttachment(Attachment $attachment)
    {
        if (!Storage::exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::download(
            $attachment->file_path,
            $attachment->original_filename
        );
    }

    /**
     * Get section summary data
     */
    public function getSummary(WorkSection $section)
    {
        $summary = [
            'total_income'           => $section->incomeItems()->sum('amount'),
            'total_expenses_inc_gst' => $section->expenseItems()->sum('amount_inc_gst'),
            'total_expenses_gst'     => $section->expenseItems()->sum('gst_amount'),
            'total_expenses_net'     => $section->expenseItems()->sum('net_ex_gst'),
            'net_profit'             => 0,
            'quarterly_breakdown'    => []
        ];

        $summary['net_profit'] = $summary['total_income'] - $summary['total_expenses_net'];

        // Get quarterly breakdown if applicable
        if ($section->requiresQuarterly()) {
            foreach (['q1', 'q2', 'q3', 'q4'] as $quarter) {
                $summary['quarterly_breakdown'][$quarter] = [
                    'income' => $section->incomeItems()->where('quarter', $quarter)->sum('amount'),
                    'expenses' => $section->expenseItems()->where('quarter', $quarter)->sum('net_ex_gst'),
                ];
                $summary['quarterly_breakdown'][$quarter]['profit'] =
                    $summary['quarterly_breakdown'][$quarter]['income'] -
                    $summary['quarterly_breakdown'][$quarter]['expenses'];
            }
        }

        return response()->json([
            'success' => true,
            'summary' => $summary
        ]);
    }

    /**
     * Get all income items for a section
     */
    public function getIncome(WorkSection $section, Request $request)
    {
        $query = $section->incomeItems()->with('attachments');

        // Filter by quarter if provided
        if ($request->has('quarter') && $request->quarter !== 'all') {
            $query->where('quarter', $request->quarter);
        }

        // Filter by rental property if provided
        if ($request->has('rental_property_id')) {
            $query->where('rental_property_id', $request->rental_property_id);
        }

        $income = $query->get();

        return response()->json([
            'success' => true,
            'income'  => $income
        ]);
    }

    /**
     * Get all expense items for a section
     */
    public function getExpenses(WorkSection $section, Request $request)
    {
        $query = $section->expenseItems()->with('attachments');

        // Filter by quarter if provided
        if ($request->has('quarter') && $request->quarter !== 'all') {
            $query->where('quarter', $request->quarter);
        }

        // Filter by label if provided (e.g., 'g1' or 'g11' for BAS)
        if ($request->has('label')) {
            $query->where('label', $request->label);
        }

        // Filter by rental property if provided
        if ($request->has('rental_property_id')) {
            $query->where('rental_property_id', $request->rental_property_id);
        }

        $expenses = $query->get();

        return response()->json([
            'success'  => true,
            'expenses' => $expenses
        ]);
    }

    /**
     * Get all rental properties for a section
     */
    public function getRentalProperties(WorkSection $section)
    {
        $properties = $section->rentalProperties()
            ->with(['incomeItems.attachments', 'expenseItems.attachments'])
            ->get();

        return response()->json([
            'success' => true,
            'properties' => $properties
        ]);
    }

    /**
     * Store a new rental property
     */
    public function storeRentalProperty(Request $request, WorkSection $section)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'address_label' => 'nullable|string|max:255',
            'full_address' => 'nullable|string|max:500',
            'ownership_percentage' => 'nullable|numeric|min:0|max:100',
            'period_rented_from' => 'nullable|date',
            'period_rented_to' => 'nullable|date|after_or_equal:period_rented_from',
        ]);

        $property = $section->rentalProperties()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rental property created successfully',
            'property' => $property
        ]);
    }

    /**
     * Update a rental property
     */
    public function updateRentalProperty(Request $request, WorkSection $section, RentalProperty $property)
    {
        // Verify property belongs to this section
        if ($property->work_section_id !== $section->id) {
            return response()->json([
                'success' => false,
                'message' => 'Property does not belong to this section'
            ], 403);
        }

        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'address_label' => 'nullable|string|max:255',
            'full_address' => 'nullable|string|max:500',
            'ownership_percentage' => 'nullable|numeric|min:0|max:100',
            'period_rented_from' => 'nullable|date',
            'period_rented_to' => 'nullable|date|after_or_equal:period_rented_from',
        ]);

        $property->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rental property updated successfully',
            'property' => $property->fresh()
        ]);
    }

    /**
     * Delete a rental property
     */
    public function destroyRentalProperty(WorkSection $section, RentalProperty $property)
    {
        // Verify property belongs to this section
        if ($property->work_section_id !== $section->id) {
            return response()->json([
                'success' => false,
                'message' => 'Property does not belong to this section'
            ], 403);
        }

        // Delete the property (cascade will handle income/expenses)
        $property->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rental property deleted successfully'
        ]);
    }
}
