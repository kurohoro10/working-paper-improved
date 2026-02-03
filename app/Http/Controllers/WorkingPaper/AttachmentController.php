<?php

namespace App\Http\Controllers\WorkingPaper;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\ExpenseItem;
use App\Models\IncomeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Controller for managing file attachments
 *
 * Handles file uploads for expense and income items with proper validation,
 * security checks, and storage management.
 */
class AttachmentController extends Controller
{
    /**
     * Maximum file size in KB (from config)
     */
    private int $maxFileSize;

    /**
     * Allowed file extensions
     */
    private array $allowedExtensions;

    public function __construct()
    {
        $this->middleware('auth');

        $this->maxFileSize = config('working-paper.max_file_upload_size', 10240); // 10MB default
        $this->allowedExtensions = explode(',', config('working-paper.allowed_file_extensions', 'pdf,jpg,jpeg,png,doc,docx,xls,xlsx'));
    }

    /**
     * Upload attachment for an expense item
     */
    public function uploadForExpense(Request $request, ExpenseItem $expense)
    {
        $this->authorize('update', $expense->workSection->workingPaper);

        return $this->handleUpload($request, $expense, 'expense');
    }

    /**
     * Upload attachment for an income item
     */
    public function uploadForIncome(Request $request, IncomeItem $income)
    {
        $this->authorize('update', $income->workSection->workingPaper);

        return $this->handleUpload($request, $income, 'income');
    }

    /**
     * Handle file upload logic (shared between expense and income)
     *
     * Process:
     * 1. Validate file (size, type, extension)
     * 2. Generate unique filename to prevent collisions
     * 3. Store file in organized directory structure
     * 4. Create attachment record in database
     * 5. Return attachment metadata
     */
    private function handleUpload(Request $request, $attachable, string $type)
    {
        // Build validation rules dynamically
        $maxSizeInKB = $this->maxFileSize;
        $allowedMimes = $this->getMimeTypes();

        $request->validate([
            'file' => [
                'required',
                'file',
                "max:{$maxSizeInKB}",
                "mimes:" . implode(',', $this->allowedExtensions),
            ],
        ], [
            'file.max' => "File size must not exceed " . ($maxSizeInKB / 1024) . "MB",
            'file.mimes' => "Only the following file types are allowed: " . implode(', ', $this->allowedExtensions),
        ]);

        $file = $request->file('file');

        /**
         * Generate unique filename to prevent collisions
         * Format: {timestamp}_{random}_{original_name}
         * This ensures uniqueness even with concurrent uploads
         */
        $timestamp = now()->timestamp;
        $randomString = Str::random(8);
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $sanitizedName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
        $storedFilename = "{$timestamp}_{$randomString}_{$sanitizedName}.{$extension}";

        /**
         * Organize files by work section and type for easier management
         * Structure: working-papers/{work_section_id}/{type}/{filename}
         */
        $workSectionId = $type === 'expense'
            ? $attachable->workSection->id
            : $attachable->workSection->id;

        $directory = "working-papers/{$workSectionId}/{$type}";

        /**
         * Store file using Laravel's storage facade
         * By default, this uses the 'local' disk (storage/app)
         */
        $filePath = $file->storeAs($directory, $storedFilename);

        /**
         * Create attachment record with metadata
         * This uses polymorphic relationship (attachable_type, attachable_id)
         */
        $attachment = $attachable->attachments()->create([
            'original_filename' => $originalName,
            'stored_filename' => $storedFilename,
            'file_path' => $filePath,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => Auth::id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'attachment' => $attachment,
                'download_url' => $attachment->getDownloadUrl(),
            ], 201);
        }

        return redirect()->back()->with('success', 'File uploaded successfully!');
    }

    /**
     * Download/view an attachment
     *
     * Security: Verify user has permission to access the working paper
     */
    public function download(Attachment $attachment)
    {
        // Get the working paper through the polymorphic relationship
        $attachable = $attachment->attachable;
        $workingPaper = null;

        if ($attachable instanceof ExpenseItem || $attachable instanceof IncomeItem) {
            $workingPaper = $attachable->workSection->workingPaper;
        }

        if (!$workingPaper) {
            abort(404);
        }

        /**
         * Authorization check:
         * - Internal users can access any attachment
         * - Clients can only access attachments from their own working papers
         */
        $user = Auth::user();
        if ($user && $user->isClient() && $workingPaper->client_id !== $user->id) {
            abort(403, 'Unauthorized access to this file.');
        }

        /**
         * Check if file exists in storage
         * If not, return 404 with helpful error message
         */
        if (!Storage::exists($attachment->file_path)) {
            abort(404, 'File not found in storage.');
        }

        /**
         * Return file as download
         * The browser will prompt to save or display the file
         */
        return Storage::download(
            $attachment->file_path,
            $attachment->original_filename
        );
    }

    /**
     * Delete an attachment
     */
    public function destroy(Attachment $attachment)
    {
        // Get working paper for authorization
        $attachable = $attachment->attachable;
        $workingPaper = null;

        if ($attachable instanceof ExpenseItem || $attachable instanceof IncomeItem) {
            $workingPaper = $attachable->workSection->workingPaper;
        }

        if (!$workingPaper) {
            abort(404);
        }

        $this->authorize('update', $workingPaper);

        /**
         * Delete attachment
         * The model's deleting event will handle removing the physical file
         */
        $attachment->delete();

        return redirect()->back()->with('success', 'Attachment deleted successfully!');
    }

    /**
     * Get MIME types for allowed extensions
     */
    private function getMimeTypes(): array
    {
        $mimeMap = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        $mimes = [];
        foreach ($this->allowedExtensions as $ext) {
            if (isset($mimeMap[$ext])) {
                $mimes[] = $mimeMap[$ext];
            }
        }

        return $mimes;
    }
}
