<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\DataFixtures;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserFixtures.
 */
class UserFixtures extends Fixture
{
    /**
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Load user data fixtures.
     *
     * Creates and persists example user and admin accounts into the database.
     *
     * @param ObjectManager $manager The object manager used for persisting entities
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 1; ++$i) {
            $user = new User();
            $user->setEmail(sprintf('user%d@example.com', $i));
            $user->setRoles([UserRole::ROLE_USER->value]);
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    'user1234'
                )
            );
            $manager->persist($user);
        }

        for ($i = 0; $i < 1; ++$i) {
            $user = new User();
            $user->setEmail(sprintf('admin%d@example.com', $i));
            $user->setRoles([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    'admin1234'
                )
            );
            $manager->persist($user);
        }

        $manager->flush();
    }
}
