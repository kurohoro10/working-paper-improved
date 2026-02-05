<?php

namespace App\Models;

use App\Enums\ExpenseFieldType;
use App\Enums\Quarter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ExpenseItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'label',
        'work_section_id',
        'rental_property_id',
        'description',
        'field_type',
        'quarter',
        'amount_inc_gst',
        'gst_amount',
        'net_ex_gst',
        'is_gst_free',
        'client_comment',
        'own_comment',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'field_type' => ExpenseFieldType::class,
        'quarter' => Quarter::class,
        'amount_inc_gst' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'net_ex_gst' => 'decimal:2',
        'is_gst_free' => 'boolean',
    ];

    /**
     * Boot the model and add event listeners
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * Before saving, auto-calculate GST if needed
         * This ensures data consistency when only some fields are provided
         */
        static::saving(function ($expense) {
            // If marked as GST free, set GST to 0 and net equals amount_inc_gst
            if ($expense->is_gst_free) {
                $expense->gst_amount = 0;
                $expense->net_ex_gst = $expense->amount_inc_gst;
                return;
            }

            // Auto-calculate if amount_inc_gst is set but GST/net are not
            if ($expense->amount_inc_gst && !$expense->gst_amount && !$expense->net_ex_gst) {
                $gstRate = 0.10; // 10% GST
                $expense->net_ex_gst = round($expense->amount_inc_gst / 1.1, 2);
                $expense->gst_amount = round($expense->amount_inc_gst - $expense->net_ex_gst, 2);
            }

            // Validate that the amounts add up correctly (with small tolerance for rounding)
            if ($expense->amount_inc_gst && $expense->gst_amount && $expense->net_ex_gst) {
                $calculatedTotal = $expense->net_ex_gst + $expense->gst_amount;
                $difference = abs($calculatedTotal - $expense->amount_inc_gst);

                // Allow tolerance of 1 cent for rounding differences
                if ($difference > 0.01) {
                    throw new \Exception(
                        "GST calculation error: Amount inc GST ({$expense->amount_inc_gst}) " .
                        "must equal Net ({$expense->net_ex_gst}) + GST ({$expense->gst_amount}). " .
                        "Difference: {$difference}"
                    );
                }
            }
        });
    }

    /**
     * Get the work section this expense belongs to
     */
    public function workSection(): BelongsTo
    {
        return $this->belongsTo(WorkSection::class);
    }

    /**
     * Get the rental property this expense belongs to (if applicable)
     */
    public function rentalProperty(): BelongsTo
    {
        return $this->belongsTo(RentalProperty::class);
    }

    /**
     * Get all attachments for this expense item (polymorphic)
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Manually calculate and set GST values based on amount_inc_gst
     */
    public function calculateGST(float $gstRate = 0.10): void
    {
        if ($this->is_gst_free) {
            $this->gst_amount = 0;
            $this->net_ex_gst = $this->amount_inc_gst;
        } else {
            $this->net_ex_gst = round($this->amount_inc_gst / (1 + $gstRate), 2);
            $this->gst_amount = round($this->amount_inc_gst - $this->net_ex_gst, 2);
        }
    }

    /**
     * Check if this expense has required attachments
     */
    public function hasAttachments(): bool
    {
        return $this->attachments()->exists();
    }

    /**
     * Scope to filter by quarter
     */
    public function scopeQuarter($query, string $quarter)
    {
        return $query->where('quarter', $quarter);
    }

    /**
     * Scope to filter by field type
     */
    public function scopeFieldType($query, string $fieldType)
    {
        return $query->where('field_type', $fieldType);
    }

    /**
     * Scope to filter by specific quarters only (exclude 'all')
     */
    public function scopeSpecificQuarters($query)
    {
        return $query->whereIn('quarter', Quarter::quarterlyValues());
    }

    /**
     * Scope to filter GST-free expenses
     */
    public function scopeGstFree($query)
    {
        return $query->where('is_gst_free', true);
    }
}
