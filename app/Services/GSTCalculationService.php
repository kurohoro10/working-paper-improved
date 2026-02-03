<?php

namespace App\Services;

/**
 * Service for handling GST calculations
 *
 * This service provides methods to calculate GST amounts, validate GST breakdowns,
 * and handle GST-free scenarios. The standard GST rate in Australia is 10%.
 */
class GSTCalculationService
{
    /**
     * Standard GST rate (10% in Australia)
     */
    private const GST_RATE = 0.10;

    /**
     * Tolerance for rounding differences (1 cent)
     */
    private const ROUNDING_TOLERANCE = 0.01;

    /**
     * Calculate GST breakdown from amount including GST
     *
     * @param float $amountIncGst Amount including GST
     * @param bool $isGstFree Whether the amount is GST-free
     * @return array ['amount_inc_gst', 'gst_amount', 'net_ex_gst']
     */
    public function calculateFromIncludingGst(float $amountIncGst, bool $isGstFree = false): array
    {
        if ($isGstFree) {
            return [
                'amount_inc_gst' => round($amountIncGst, 2),
                'gst_amount' => 0.00,
                'net_ex_gst' => round($amountIncGst, 2),
            ];
        }

        // Calculate net amount: amount_inc_gst / 1.1
        $netExGst = round($amountIncGst / (1 + self::GST_RATE), 2);

        // Calculate GST: amount_inc_gst - net
        $gstAmount = round($amountIncGst - $netExGst, 2);

        return [
            'amount_inc_gst' => round($amountIncGst, 2),
            'gst_amount' => $gstAmount,
            'net_ex_gst' => $netExGst,
        ];
    }

    /**
     * Calculate amount including GST from net amount
     *
     * @param float $netExGst Net amount excluding GST
     * @param bool $isGstFree Whether the amount is GST-free
     * @return array ['amount_inc_gst', 'gst_amount', 'net_ex_gst']
     */
    public function calculateFromExcludingGst(float $netExGst, bool $isGstFree = false): array
    {
        if ($isGstFree) {
            return [
                'amount_inc_gst' => round($netExGst, 2),
                'gst_amount' => 0.00,
                'net_ex_gst' => round($netExGst, 2),
            ];
        }

        // Calculate GST: net * 0.1
        $gstAmount = round($netExGst * self::GST_RATE, 2);

        // Calculate total: net + GST
        $amountIncGst = round($netExGst + $gstAmount, 2);

        return [
            'amount_inc_gst' => $amountIncGst,
            'gst_amount' => $gstAmount,
            'net_ex_gst' => round($netExGst, 2),
        ];
    }

    /**
     * Validate that GST breakdown is correct
     *
     * Checks that: amount_inc_gst = net_ex_gst + gst_amount
     * Allows for small rounding differences (1 cent)
     *
     * @param float $amountIncGst Amount including GST
     * @param float $gstAmount GST amount
     * @param float $netExGst Net amount excluding GST
     * @return bool True if valid, false otherwise
     */
    public function validateGstBreakdown(float $amountIncGst, float $gstAmount, float $netExGst): bool
    {
        $calculatedTotal = $netExGst + $gstAmount;
        $difference = abs($calculatedTotal - $amountIncGst);

        // Allow tolerance for rounding (1 cent)
        return $difference <= self::ROUNDING_TOLERANCE;
    }

    /**
     * Get validation error message if GST breakdown is invalid
     *
     * @param float $amountIncGst Amount including GST
     * @param float $gstAmount GST amount
     * @param float $netExGst Net amount excluding GST
     * @return string|null Error message or null if valid
     */
    public function getValidationError(float $amountIncGst, float $gstAmount, float $netExGst): ?string
    {
        if ($this->validateGstBreakdown($amountIncGst, $gstAmount, $netExGst)) {
            return null;
        }

        $calculatedTotal = $netExGst + $gstAmount;
        $difference = abs($calculatedTotal - $amountIncGst);

        return sprintf(
            'GST calculation error: Amount inc GST ($%.2f) must equal Net ($%.2f) + GST ($%.2f). Difference: $%.2f',
            $amountIncGst,
            $netExGst,
            $gstAmount,
            $difference
        );
    }

    /**
     * Calculate totals for multiple expense items
     *
     * @param array $expenses Array of expense data with GST breakdown
     * @return array ['total_inc_gst', 'total_gst', 'total_net']
     */
    public function calculateTotals(array $expenses): array
    {
        $totalIncGst = 0;
        $totalGst = 0;
        $totalNet = 0;

        foreach ($expenses as $expense) {
            $totalIncGst += $expense['amount_inc_gst'] ?? 0;
            $totalGst += $expense['gst_amount'] ?? 0;
            $totalNet += $expense['net_ex_gst'] ?? 0;
        }

        return [
            'total_inc_gst' => round($totalIncGst, 2),
            'total_gst' => round($totalGst, 2),
            'total_net' => round($totalNet, 2),
        ];
    }

    /**
     * Get the current GST rate
     *
     * @return float GST rate as decimal (e.g., 0.10 for 10%)
     */
    public function getGstRate(): float
    {
        return self::GST_RATE;
    }

    /**
     * Get the current GST rate as percentage
     *
     * @return int GST rate as percentage (e.g., 10)
     */
    public function getGstRatePercentage(): int
    {
        return (int)(self::GST_RATE * 100);
    }
}
