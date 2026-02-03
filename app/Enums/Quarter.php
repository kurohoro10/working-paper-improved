<?php

namespace App\Enums;

enum Quarter: string
{
    case ALL = 'all';
    case Q1 = 'q1';
    case Q2 = 'q2';
    case Q3 = 'q3';
    case Q4 = 'q4';

    /**
     * Get all quarter values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get quarterly values only (excluding ALL)
     */
    public static function quarterlyValues(): array
    {
        return [
            self::Q1->value,
            self::Q2->value,
            self::Q3->value,
            self::Q4->value,
        ];
    }

    /**
     * Get display label
     */
    public function label(): string
    {
        return match($this) {
            self::ALL => 'All Quarters',
            self::Q1 => 'Quarter 1 (Jan-Mar)',
            self::Q2 => 'Quarter 2 (Apr-Jun)',
            self::Q3 => 'Quarter 3 (Jul-Sep)',
            self::Q4 => 'Quarter 4 (Oct-Dec)',
        };
    }

    /**
     * Get short label
     */
    public function shortLabel(): string
    {
        return match($this) {
            self::ALL => 'All',
            self::Q1 => 'Q1',
            self::Q2 => 'Q2',
            self::Q3 => 'Q3',
            self::Q4 => 'Q4',
        };
    }

    /**
     * Check if this is a specific quarter (not ALL)
     */
    public function isSpecificQuarter(): bool
    {
        return $this !== self::ALL;
    }
}
