<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingPaper extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference_number',
        'client_id',
        'created_by',
        'financial_year',
        'selected_work_types',
        'status',
        'notes',
        'access_token',
        'token_expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'selected_work_types' => 'array',
        'token_expires_at' => 'datetime',
    ];

    /**
     * Boot the model and add event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // When creating a new working paper, generate reference number and token
        static::creating(function ($workingPaper) {
            if (empty($workingPaper->reference_number)) {
                $workingPaper->reference_number = static::generateReferenceNumber();
            }

            if (empty($workingPaper->access_token)) {
                $workingPaper->access_token = static::generateAccessToken();

                $expiryDays = (int) config('working-paper.token_expiry_days', 30);
                $workingPaper->token_expires_at = now()->addDays($expiryDays);
            }
        });
    }

    /**
     * Get the client user
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the user who created this working paper
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get work sections for this working paper
     */
    public function workSections(): HasMany
    {
        return $this->hasMany(WorkSection::class);
    }

    /**
     * Generate a unique reference number
     * Format: WP-YYYY-NNNNN (e.g., WP-2024-00001)
     */
    public static function generateReferenceNumber(): string
    {
        $prefix = config('working-paper.reference_prefix', 'WP');
        $year = now()->year;

        // Get the last reference number for this year
        $lastRecord = static::withTrashed()
            ->where('reference_number', 'like', "{$prefix}-{$year}-%")
            ->orderBy('reference_number', 'desc')
            ->first();

        if ($lastRecord) {
            // Extract the sequence number and increment
            $lastNumber = (int) substr($lastRecord->reference_number, -5);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('%s-%d-%05d', $prefix, $year, $nextNumber);
    }

    /**
     * Generate a secure access token for guest access
     */
    public static function generateAccessToken(): string
    {
        return bin2hex(random_bytes(32)); // 64 character hex string
    }

    /**
     * Check if the access token is valid
     */
    public function isTokenValid(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isFuture();
    }

    /**
     * Regenerate access token
     */
    public function regenerateToken(): void
    {
        $this->access_token = static::generateAccessToken();
        $expiryDays = (int) config('working-paper.token_expiry_days', 30);
        $this->token_expires_at = now()->addDays($expiryDays);
        $this->save();
    }

    /**
     * Get the public access URL for this working paper
     */
    public function getPublicUrl(): string
    {
        return route('working-paper.guest.view', [
            'reference' => $this->reference_number,
            'token' => $this->access_token,
        ]);
    }

    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by financial year
     */
    public function scopeFinancialYear($query, string $year)
    {
        return $query->where('financial_year', $year);
    }

    /**
     * Scope to filter by client
     */
    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Check if a specific work type is enabled
     */
    public function hasWorkType(string $workType): bool
    {
        return in_array($workType, $this->selected_work_types ?? []);
    }

    /**
     * Get or create a work section for a specific work type
     */
    public function getOrCreateWorkSection(string $workType): WorkSection
    {
        return $this->workSections()->firstOrCreate(
            ['work_type' => $workType],
            ['data' => []]
        );
    }
}
