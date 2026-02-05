<?php

namespace App\Enums;

enum ExpenseFieldType: string
{
    case TYPE_A = 'A';
    case TYPE_B = 'B';
    case TYPE_C = 'C';

    /**
     * Get all field type values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get display label
     */
    public function label(): string
    {
        return match($this) {
            self::TYPE_A => 'A',
            self::TYPE_B => 'B',
            self::TYPE_C => 'C',
        };
    }
}
