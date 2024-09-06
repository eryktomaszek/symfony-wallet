<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class TransactionFixtures.
 */
class TransactionFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Get the dependencies for this fixture.
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            WalletFixtures::class,
            TagFixtures::class,
            UserFixtures::class,
        ];
    }

    /**
     * Load data.
     */
    protected function loadData(): void
    {
    }
}
