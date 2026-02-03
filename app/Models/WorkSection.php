<?php

namespace App\Models;

use App\Enums\WorkType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkSection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'working_paper_id',
        'work_type',
        'data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'work_type' => WorkType::class,
        'data' => 'array',
    ];

    /**
     * Get the working paper this section belongs to
     */
    public function workingPaper(): BelongsTo
    {
        return $this->belongsTo(WorkingPaper::class);
    }

    /**
     * Get rental properties for this section (if work_type is rental_property)
     */
    public function rentalProperties(): HasMany
    {
        return $this->hasMany(RentalProperty::class);
    }

    /**
     * Get income items for this section
     */
    public function incomeItems(): HasMany
    {
        return $this->hasMany(IncomeItem::class);
    }

    /**
     * Get expense items for this section
     */
    public function expenseItems(): HasMany
    {
        return $this->hasMany(ExpenseItem::class);
    }

    /**
     * Check if this section requires GST calculations
     */
    public function requiresGST(): bool
    {
        return $this->work_type->requiresGST();
    }

    /**
     * Check if this section requires quarterly tracking
     */
    public function requiresQuarterly(): bool
    {
        return $this->work_type->requiresQuarterly();
    }

    /**
     * Check if this section requires field type (A/B/C)
     */
    public function requiresFieldType(): bool
    {
        return $this->work_type->requiresFieldType();
    }

    /**
     * Get total income for this section
     */
    public function getTotalIncome(?string $quarter = null): float
    {
        $query = $this->incomeItems();

        if ($quarter) {
            $query->where('quarter', $quarter);
        }

        return $query->sum('amount');
    }

    /**
     * Get total expenses for this section
     */
    public function getTotalExpenses(?string $quarter = null, string $field = 'amount_inc_gst'): float
    {
        $query = $this->expenseItems();

        if ($quarter) {
            $query->where('quarter', $quarter);
        }

        return $query->sum($field);
    }

    /**
     * Get net profit (income - expenses)
     */
    public function getNetProfit(?string $quarter = null): float
    {
        $income = $this->getTotalIncome($quarter);
        $expenses = $this->getTotalExpenses($quarter, 'net_ex_gst');

        return $income - $expenses;
    }

    /**
     * Get totals by quarter
     */
    public function getQuarterlyTotals(): array
    {
        $quarters = ['q1', 'q2', 'q3', 'q4'];
        $totals = [];

        foreach ($quarters as $quarter) {
            $totals[$quarter] = [
                'income' => $this->getTotalIncome($quarter),
                'expenses_inc_gst' => $this->getTotalExpenses($quarter, 'amount_inc_gst'),
                'expenses_gst' => $this->getTotalExpenses($quarter, 'gst_amount'),
                'expenses_net' => $this->getTotalExpenses($quarter, 'net_ex_gst'),
                'net_profit' => $this->getNetProfit($quarter),
            ];
        }

        return $totals;
    }

    /**
     * Scope to filter by work type
     */
    public function scopeOfType($query, WorkType $workType)
    {
        return $query->where('work_type', $workType->value);
    }
}
