<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case ENDUREGO_INTERNAL = 'endurego_internal';
    case CLIENT = 'client';

    /**
     * Get all role values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get role label for display
     */
    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::ENDUREGO_INTERNAL => 'EndureGo Internal',
            self::CLIENT => 'Client',
        };
    }

    /**
     * Check if role can create working papers
     */
    public function canCreateWorkingPaper(): bool
    {
        return in_array($this, [self::ADMIN, self::ENDUREGO_INTERNAL]);
    }

    /**
     * Check if role has admin privileges
     */
    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Check if role is internal staff
     */
    public function isInternal(): bool
    {
        return in_array($this, [self::ADMIN, self::ENDUREGO_INTERNAL]);
    }
}
