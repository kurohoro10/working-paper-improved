<?php

namespace App\Services;

use App\Enums\Quarter;
use App\Enums\WorkType;
use App\Models\ExpenseItem;
use App\Models\IncomeItem;
use App\Models\User;
use App\Models\WorkingPaper;
use App\Models\WorkSection;
use Illuminate\Support\Facades\DB;

/**
 * Service for managing working papers
 *
 * This service handles the creation, updating, and management of working papers
 * including work sections, income/expense items, and quarterly consolidation.
 */
class WorkingPaperService
{
    public function __construct(
        private GSTCalculationService $gstService
    ) {}

    /**
     * Create a new working paper
     *
     * @param array $data Working paper data
     * @param User $creator User creating the working paper
     * @return WorkingPaper
     */
    public function create(array $data, User $creator): WorkingPaper
    {
        return DB::transaction(function () use ($data, $creator) {
            // Create the working paper
            $workingPaper = WorkingPaper::create([
                'client_id' => $data['client_id'],
                'created_by' => $creator->id,
                'financial_year' => $data['financial_year'],
                'selected_work_types' => $data['selected_work_types'] ?? [],
                'status' => $data['status'] ?? 'draft',
                'notes' => $data['notes'] ?? null,
            ]);

            // Create work sections for all selected work types
            // This ensures all work types are accessible immediately after creation
            foreach ($workingPaper->selected_work_types as $workType) {
                $workingPaper->workSections()->create([
                    'work_type' => $workType,
                    'data' => [],
                ]);
            }

            return $workingPaper->fresh(['workSections', 'client', 'creator']);
        });
    }

    /**
     * Update working paper details
     *
     * @param WorkingPaper $workingPaper
     * @param array $data
     * @return WorkingPaper
     */
    public function update(WorkingPaper $workingPaper, array $data): WorkingPaper
    {
        return DB::transaction(function () use ($workingPaper, $data) {
            $workingPaper->update([
                'financial_year' => $data['financial_year'] ?? $workingPaper->financial_year,
                'status' => $data['status'] ?? $workingPaper->status,
                'notes' => $data['notes'] ?? $workingPaper->notes,
            ]);

            // Handle work type changes
            if (isset($data['selected_work_types'])) {
                $this->syncWorkTypes($workingPaper, $data['selected_work_types']);
            }

            return $workingPaper->fresh();
        });
    }

    /**
     * Sync work types - add new ones, keep existing ones
     *
     * Note: We don't delete work sections to preserve data,
     * we just create new ones for newly added types
     *
     * @param WorkingPaper $workingPaper
     * @param array $selectedWorkTypes
     */
    private function syncWorkTypes(WorkingPaper $workingPaper, array $selectedWorkTypes): void
    {
        $workingPaper->selected_work_types = $selectedWorkTypes;
        $workingPaper->save();

        $existingTypes = $workingPaper->workSections->pluck('work_type')->map(fn($t) => $t->value)->toArray();

        // Create sections for new work types
        foreach ($selectedWorkTypes as $workType) {
            if (!in_array($workType, $existingTypes)) {
                $workingPaper->workSections()->create([
                    'work_type' => $workType,
                    'data' => [],
                ]);
            }
        }
    }

    /**
     * Combine quarterly data into annual summary
     *
     * This method aggregates Q1-Q4 data for work types that support quarterly tracking.
     * It validates that all 4 quarters have data and excludes 'all' quarter entries
     * to prevent double-counting.
     *
     * @param WorkSection $workSection
     * @return array Annual totals by quarter
     * @throws \Exception If not all quarters have data
     */
    public function combineQuarters(WorkSection $workSection): array
    {
        if (!$workSection->requiresQuarterly()) {
            throw new \Exception("Work type {$workSection->work_type->value} does not support quarterly tracking");
        }

        $quarters = Quarter::quarterlyValues();
        $quarterlyData = [];
        $annualTotals = [
            'income' => 0,
            'expenses_inc_gst' => 0,
            'expenses_gst' => 0,
            'expenses_net' => 0,
            'net_profit' => 0,
        ];

        // Check if we have data for all quarters (Q1-Q4)
        foreach ($quarters as $quarter) {
            $incomeCount = $workSection->incomeItems()->where('quarter', $quarter)->count();
            $expenseCount = $workSection->expenseItems()->where('quarter', $quarter)->count();

            if ($incomeCount === 0 && $expenseCount === 0) {
                throw new \Exception("Missing data for {$quarter}. All quarters (Q1-Q4) must have data to combine.");
            }
        }

        // Calculate totals for each quarter
        foreach ($quarters as $quarter) {
            $income = $workSection->incomeItems()
                ->where('quarter', $quarter)
                ->sum('amount');

            $expensesIncGst = $workSection->expenseItems()
                ->where('quarter', $quarter)
                ->sum('amount_inc_gst');

            $expensesGst = $workSection->expenseItems()
                ->where('quarter', $quarter)
                ->sum('gst_amount');

            $expensesNet = $workSection->expenseItems()
                ->where('quarter', $quarter)
                ->sum('net_ex_gst');

            $quarterlyData[$quarter] = [
                'income' => $income,
                'expenses_inc_gst' => $expensesIncGst,
                'expenses_gst' => $expensesGst,
                'expenses_net' => $expensesNet,
                'net_profit' => $income - $expensesNet,
            ];

            // Add to annual totals
            $annualTotals['income'] += $income;
            $annualTotals['expenses_inc_gst'] += $expensesIncGst;
            $annualTotals['expenses_gst'] += $expensesGst;
            $annualTotals['expenses_net'] += $expensesNet;
            $annualTotals['net_profit'] += ($income - $expensesNet);
        }

        return [
            'quarterly' => $quarterlyData,
            'annual' => $annualTotals,
        ];
    }

    /**
     * Get summary statistics for a working paper
     *
     * @param WorkingPaper $workingPaper
     * @return array Summary data
     */
    public function getSummary(WorkingPaper $workingPaper): array
    {
        $summary = [
            'total_sections' => $workingPaper->workSections()->count(),
            'total_income_items' => 0,
            'total_expense_items' => 0,
            'total_income' => 0,
            'total_expenses' => 0,
            'by_work_type' => [],
        ];

        foreach ($workingPaper->workSections as $section) {
            $incomeCount = $section->incomeItems()->count();
            $expenseCount = $section->expenseItems()->count();
            $income = $section->getTotalIncome();
            $expenses = $section->getTotalExpenses(null, 'net_ex_gst');

            $summary['total_income_items'] += $incomeCount;
            $summary['total_expense_items'] += $expenseCount;
            $summary['total_income'] += $income;
            $summary['total_expenses'] += $expenses;

            $summary['by_work_type'][$section->work_type->value] = [
                'income_items' => $incomeCount,
                'expense_items' => $expenseCount,
                'total_income' => $income,
                'total_expenses' => $expenses,
                'net_profit' => $income - $expenses,
            ];
        }

        $summary['net_profit'] = $summary['total_income'] - $summary['total_expenses'];

        return $summary;
    }

    /**
     * Regenerate access token for a working paper
     *
     * @param WorkingPaper $workingPaper
     * @return WorkingPaper
     */
    public function regenerateAccessToken(WorkingPaper $workingPaper): WorkingPaper
    {
        $workingPaper->regenerateToken();
        return $workingPaper;
    }

    /**
     * Validate that expense items have required attachments
     *
     * @param WorkingPaper $workingPaper
     * @return array ['valid' => bool, 'missing' => array of expense IDs]
     */
    public function validateAttachments(WorkingPaper $workingPaper): array
    {
        $missing = [];

        foreach ($workingPaper->workSections as $section) {
            foreach ($section->expenseItems as $expense) {
                if (!$expense->hasAttachments()) {
                    $missing[] = $expense->id;
                }
            }
        }

        return [
            'valid' => empty($missing),
            'missing' => $missing,
            'count' => count($missing),
        ];
    }
}
