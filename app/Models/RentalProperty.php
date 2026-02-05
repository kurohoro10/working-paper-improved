<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentalProperty extends Model
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
        'address_label',
        'full_address',
        'ownership_percentage',
        'period_rented_from',
        'period_rented_to',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ownership_percentage' => 'decimal:2',
        'period_rented_from' => 'date',
        'period_rented_to' => 'date',
    ];

    /**
     * Get the work section this property belongs to
     */
    public function workSection(): BelongsTo
    {
        return $this->belongsTo(WorkSection::class);
    }

    /**
     * Get income items for this property
     */
    public function incomeItems(): HasMany
    {
        return $this->hasMany(IncomeItem::class);
    }

    /**
     * Get expense items for this property
     */
    public function expenseItems(): HasMany
    {
        return $this->hasMany(ExpenseItem::class);
    }

    /**
     * Get total income for this property
     */
    public function getTotalIncome(): float
    {
        return $this->incomeItems()->sum('amount');
    }

    /**
     * Get total expenses for this property
     */
    public function getTotalExpenses(): float
    {
        return $this->expenseItems()->sum('amount_inc_gst');
    }

    /**
     * Get net profit for this property
     */
    public function getNetProfit(): float
    {
        return $this->getTotalIncome() - $this->getTotalExpenses();
    }
}
