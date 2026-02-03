<?php

namespace App\Enums;

enum WorkType: string
{
    case WAGE = 'wage';
    case RENTAL_PROPERTY = 'rental_property';
    case SOLE_TRADER = 'sole_trader';
    case BAS = 'bas';
    case COMPANY_TAX = 'ctax';
    case TRUST_TAX = 'ttax';
    case SMSF = 'smsf';

    /**
     * Get all work type values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get display label for work type
     */
    public function label(): string
    {
        return match($this) {
            self::WAGE => 'Wage',
            self::RENTAL_PROPERTY => 'Rental Property',
            self::SOLE_TRADER => 'Sole Trader',
            self::BAS => 'BAS',
            self::COMPANY_TAX => 'Company Tax',
            self::TRUST_TAX => 'Trust Tax',
            self::SMSF => 'SMSF',
        };
    }

    /**
     * Check if this work type requires GST calculations
     */
    public function requiresGST(): bool
    {
        return in_array($this, [
            self::SOLE_TRADER,
            self::BAS,
            self::COMPANY_TAX,
            self::TRUST_TAX,
            self::SMSF,
        ]);
    }

    /**
     * Check if this work type requires quarterly tracking
     */
    public function requiresQuarterly(): bool
    {
        return in_array($this, [
            self::SOLE_TRADER,
            self::BAS,
            self::COMPANY_TAX,
            self::TRUST_TAX,
            self::SMSF,
        ]);
    }

    /**
     * Check if this work type requires field type (A/B/C)
     */
    public function requiresFieldType(): bool
    {
        return in_array($this, [
            self::SOLE_TRADER,
            self::BAS,
            self::COMPANY_TAX,
            self::TRUST_TAX,
            self::SMSF,
        ]);
    }

    /**
     * Check if this work type supports income entries
     */
    public function hasIncome(): bool
    {
        return $this !== self::WAGE;
    }

    /**
     * Check if expenses are required for this type
     */
    public function requiresExpenses(): bool
    {
        return true; // All types can have expenses
    }
}
