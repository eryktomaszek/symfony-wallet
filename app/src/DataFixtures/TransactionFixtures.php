<?php

namespace App\DataFixtures;

use App\Entity\Transaction;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class TransactionFixtures.
 */
class TransactionFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     */
    protected function loadData(): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $transaction = new Transaction();
            $transaction->setAmount($this->faker->randomFloat(2, 1, 1000));
            $transaction->setDescription($this->faker->text(200));
            $transaction->setDate($this->faker->dateTimeBetween('-100 days', 'now'));
            $transaction->setType($this->faker->randomElement(['income', 'expense']));
            $transaction->setWallet($this->getReference('wallet_' . $this->faker->numberBetween(0, 2))); // Set wallet
            $transaction->setCategory($this->getReference('category_' . $this->faker->numberBetween(0, 9))); // Set category
            $this->manager->persist($transaction);
        }

        $this->manager->flush();
    }

    /**
     * Get the dependencies for this fixture.
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
            WalletFixtures::class,
        ];
    }
}
