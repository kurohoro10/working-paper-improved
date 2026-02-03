<?php

namespace App\Http\Controllers\WorkingPaper;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkingPaper;
use App\Services\WorkingPaperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for managing working papers
 *
 * This controller handles CRUD operations for working papers.
 * Only admin and endurego_internal users can create working papers.
 */
class WorkingPaperController extends Controller
{
    public function __construct(
        private WorkingPaperService $workingPaperService
    ) {
        // Apply authentication middleware
        $this->middleware('auth');

        // Only admin and endurego_internal can create/update working papers
        $this->middleware(function ($request, $next) {
            $user = Auth::user();

            // For create and store actions, check creation permission
            if (in_array($request->route()->getActionMethod(), ['create', 'store'])) {
                if (!$user->canCreateWorkingPaper()) {
                    abort(403, 'Unauthorized. Only administrators and internal employees can create working papers.');
                }
            }

            return $next($request);
        })->only(['create', 'store', 'update', 'destroy']);
    }

    /**
     * Display a listing of working papers
     *
     * - Admin/Internal: See all working papers
     * - Client: See only their own working papers
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        /**
         * Query builder with eager loading to prevent N+1 queries
         * We load client and creator relationships upfront for efficiency
         */
        $query = WorkingPaper::with(['client', 'creator'])
            ->orderBy('created_at', 'desc');

        // Filter based on user role
        if ($user->isClient()) {
            // Clients can only see their own working papers
            $query->where('client_id', $user->id);
        }

        // Apply optional filters from request
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('financial_year')) {
            $query->where('financial_year', $request->financial_year);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $workingPapers = $query->paginate(20);

        return view('working-paper.index', compact('workingPapers'));
    }

    /**
     * Show the form for creating a new working paper
     *
     * Only accessible by admin and endurego_internal users
     */
    public function create()
    {
        // Get all clients for the dropdown
        $clients = User::clients()->active()->orderBy('name')->get();

        // Get current financial year as default
        $currentYear = now()->year;
        $defaultFinancialYear = "{$currentYear}-" . ($currentYear + 1);

        return view('working-paper.create', compact('clients', 'defaultFinancialYear'));
    }

    /**
     * Store a newly created working paper
     *
     * Process:
     * 1. Validate input data
     * 2. Create working paper with auto-generated reference number
     * 3. Create work sections for all selected work types
     * 4. Generate access token for guest access
     * 5. Redirect to edit page
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'client_id' => 'required|exists:users,id',
            'financial_year' => 'required|string|max:10',
            'selected_work_types' => 'required|array|min:1',
            'selected_work_types.*' => 'string|in:wage,rental_property,sole_trader,bas,ctax,ttax,smsf',
            'notes' => 'nullable|string|max:5000',
            'status' => 'nullable|string|in:draft,in_progress,completed,archived',
        ]);

        /**
         * Create working paper using service
         * The service handles transaction management and work section creation
         */
        $workingPaper = $this->workingPaperService->create(
            $validated,
            Auth::user()
        );

        return redirect()
            ->route('working-papers.show', $workingPaper)
            ->with('success', "Working paper {$workingPaper->reference_number} created successfully!");
    }

    /**
     * Display the specified working paper
     *
     * Access control:
     * - Admin/Internal: Can view any working paper
     * - Client: Can only view their own working papers
     */
    public function show(WorkingPaper $workingPaper)
    {
        $user = Auth::user();

        /**
         * Authorization check
         * Clients can only view their own working papers
         */
        if ($user->isClient() && $workingPaper->client_id !== $user->id) {
            abort(403, 'You are not authorized to view this working paper.');
        }

        // Eager load relationships to avoid N+1 queries
        $workingPaper->load([
            'client',
            'creator',
            'workSections.incomeItems.attachments',
            'workSections.expenseItems.attachments',
            'workSections.rentalProperties.incomeItems',
            'workSections.rentalProperties.expenseItems',
        ]);

        // Get summary statistics
        $summary = $this->workingPaperService->getSummary($workingPaper);

        // Check attachment validation
        $attachmentValidation = $this->workingPaperService->validateAttachments($workingPaper);

        return view('working-paper.show', compact(
            'workingPaper',
            'summary',
            'attachmentValidation'
        ));
    }

    /**
     * Show the form for editing the specified working paper
     */
    public function edit(WorkingPaper $workingPaper)
    {
        $user = Auth::user();

        // Authorization check
        if ($user->isClient() && $workingPaper->client_id !== $user->id) {
            abort(403, 'You are not authorized to edit this working paper.');
        }

        $clients = User::clients()->active()->orderBy('name')->get();

        $workingPaper->load(['workSections']);

        return view('working-paper.edit', compact('workingPaper', 'clients'));
    }

    /**
     * Update the specified working paper
     */
    public function update(Request $request, WorkingPaper $workingPaper)
    {
        $validated = $request->validate([
            'financial_year' => 'sometimes|required|string|max:10',
            'selected_work_types' => 'sometimes|required|array|min:1',
            'selected_work_types.*' => 'string|in:wage,rental_property,sole_trader,bas,ctax,ttax,smsf',
            'notes' => 'nullable|string|max:5000',
            'status' => 'nullable|string|in:draft,in_progress,completed,archived',
        ]);

        $workingPaper = $this->workingPaperService->update($workingPaper, $validated);

        return redirect()
            ->route('working-papers.show', $workingPaper)
            ->with('success', 'Working paper updated successfully!');
    }

    /**
     * Remove the specified working paper (soft delete)
     */
    public function destroy(WorkingPaper $workingPaper)
    {
        $reference = $workingPaper->reference_number;
        $workingPaper->delete();

        return redirect()
            ->route('working-papers.index')
            ->with('success', "Working paper {$reference} has been deleted.");
    }

    /**
     * Regenerate access token for guest access
     */
    public function regenerateToken(WorkingPaper $workingPaper)
    {
        $user = Auth::user();

        // Only internal users can regenerate tokens
        if (!$user->isInternal()) {
            abort(403, 'Unauthorized action.');
        }

        $this->workingPaperService->regenerateAccessToken($workingPaper);

        return redirect()
            ->back()
            ->with('success', 'Access token regenerated successfully!');
    }

    /**
     * Get quarterly consolidation for BAS and other quarterly work types
     */
    public function quarterlyConsolidation(WorkingPaper $workingPaper, $workSectionId)
    {
        $user = Auth::user();

        // Authorization check
        if ($user->isClient() && $workingPaper->client_id !== $user->id) {
            abort(403, 'Unauthorized.');
        }

        $workSection = $workingPaper->workSections()->findOrFail($workSectionId);

        try {
            /**
             * Combine Q1-Q4 data into annual summary
             * This validates that all quarters have data and aggregates them
             */
            $consolidation = $this->workingPaperService->combineQuarters($workSection);

            return view('working-paper.quarterly-consolidation', compact(
                'workingPaper',
                'workSection',
                'consolidation'
            ));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
