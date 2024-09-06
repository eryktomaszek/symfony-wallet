<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Entity\Enum;

/**
 * Enum UserRole.
 */
enum UserRole: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * Get the role label.
     *
     * @return string Role label
     */
    public function label(): string
    {
        return match ($this) {
            UserRole::ROLE_USER => 'label.role_user',
            UserRole::ROLE_ADMIN => 'label.role_admin',
        };
    }
}
