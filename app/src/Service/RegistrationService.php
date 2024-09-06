<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class RegistrationService.
 *
 * Service to handle user registration.
 */
class RegistrationService implements RegistrationServiceInterface
{
    /**
     * RegistrationService constructor.
     *
     * @param EntityManagerInterface      $entityManager  The entity manager for database operations
     * @param UserPasswordHasherInterface $passwordHasher The password hasher for encoding user passwords
     */
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Register a new user.
     *
     * @param User $user The user to register
     */
    public function registerUser(User $user): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        );

        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
