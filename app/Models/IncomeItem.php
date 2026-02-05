<?php

namespace App\Models;

use App\Enums\Quarter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class IncomeItem extends Model
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
        'contribution',
        'interest_income',
        'dividends',
        'rental_income',
        'capital_gains',
        'amount',
        'quarter',
        'client_comment',
        'own_comment',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'quarter' => Quarter::class,
    ];

    /**
     * Get the work section this income belongs to
     */
    public function workSection(): BelongsTo
    {
        return $this->belongsTo(WorkSection::class);
    }

    /**
     * Get the rental property this income belongs to (if applicable)
     */
    public function rentalProperty(): BelongsTo
    {
        return $this->belongsTo(RentalProperty::class);
    }

    /**
     * Get all attachments for this income item (polymorphic)
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Scope to filter by quarter
     */
    public function scopeQuarter($query, string $quarter)
    {
        return $query->where('quarter', $quarter);
    }

    /**
     * Scope to filter by specific quarters only (exclude 'all')
     */
    public function scopeSpecificQuarters($query)
    {
        return $query->whereIn('quarter', Quarter::quarterlyValues());
    }
}
