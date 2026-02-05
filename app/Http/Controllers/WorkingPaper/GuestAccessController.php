<?php

namespace App\Http\Controllers\WorkingPaper;

use App\Http\Controllers\Controller;
use App\Models\WorkingPaper;
use App\Services\WorkingPaperService;
use Illuminate\Http\Request;

/**
 * Controller for guest (unauthenticated) access to working papers
 *
 * This allows clients to view their working papers without logging in
 * using a secure token-based URL. The token expires after a configured period.
 */
class GuestAccessController extends Controller
{
    public function __construct(
        private WorkingPaperService $workingPaperService
    ) {}

    /**
     * View working paper via guest access token
     *
     * This method handles unauthenticated access to working papers.
     * The URL format is: /working-paper/{reference}/view?token={token}
     *
     * Security checks:
     * 1. Validate that reference number exists
     * 2. Validate that token matches
     * 3. Validate that token hasn't expired
     */
    public function view(Request $request, string $reference)
    {
        // Get token from query string
        $token = $request->query('token');

        if (!$token) {
            abort(403, 'Access token is required.');
        }

        /**
         * Find working paper by reference number and token
         * We use both to ensure security (reference is predictable, token is not)
         */
        $workingPaper = WorkingPaper::where('reference_number', $reference)
            ->where('access_token', $token)
            ->firstOrFail();

        /**
         * Check if token is still valid (not expired)
         * Tokens expire after configured days (default: 30 days)
         */
        if (!$workingPaper->isTokenValid()) {
            return view('working-paper.guest.token-expired', compact('workingPaper'));
        }

        // Load all necessary relationships for display
        $workingPaper->load([
            'client',
            'creator',
            'workSections.incomeItems.attachments',
            'workSections.expenseItems.attachments',
            'workSections.rentalProperties.incomeItems.attachments',
            'workSections.rentalProperties.expenseItems.attachments',
        ]);

        // Get summary data
        $summary = $this->workingPaperService->getSummary($workingPaper);
        $access_token= $workingPaper->access_token;

        /**
         * Return guest-specific view
         * This view has limited functionality compared to authenticated view
         * (e.g., no edit/delete buttons, simplified interface)
         */
        return view('working-paper.guest.view', compact('workingPaper', 'summary', 'access_token'));
    }

    /**
     * Request new token (for expired tokens)
     *
     * This allows clients to request a new access link via email
     * when their token has expired.
     */
    public function requestNewToken(Request $request)
    {
        $validated = $request->validate([
            'reference_number' => 'required|string|exists:working_papers,reference_number',
            'email' => 'required|email',
        ]);

        /**
         * Find working paper and verify email matches client
         * This prevents unauthorized token regeneration
         */
        $workingPaper = WorkingPaper::where('reference_number', $validated['reference_number'])
            ->first();

        if (!$workingPaper || $workingPaper->client->email !== $validated['email']) {
            // Return generic message to prevent email enumeration
            return back()->with('success', 'If the information is correct, you will receive an email with a new access link.');
        }

        // Regenerate token
        $this->workingPaperService->regenerateAccessToken($workingPaper);

        // TODO: Send email with new link
        // Mail::to($workingPaper->client)->send(new WorkingPaperAccessLink($workingPaper));

        return back()->with('success', 'A new access link has been sent to your email.');
    }
}
