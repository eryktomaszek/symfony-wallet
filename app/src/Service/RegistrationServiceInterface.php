<?php

namespace App\Service;

use App\Entity\User;

interface RegistrationServiceInterface
{
    /**
     * Register a new user with a hashed password.
     *
     * @param User $user
     */
    public function registerUser(User $user): void;
}
