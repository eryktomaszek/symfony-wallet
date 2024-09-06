<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Service;

use App\Entity\User;

/**
 * Interface RegistrationServiceInterface.
 */
interface RegistrationServiceInterface
{
    /**
     * Register a new user.
     *
     * @param User $user The user to register
     */
    public function registerUser(User $user): void;
}
