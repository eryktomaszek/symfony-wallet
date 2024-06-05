<?php

namespace App\DataFixtures;

use App\Entity\Wallet;

/**
 * Class WalletFixtures.
 */
class WalletFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     */
    protected function loadData(): void
    {
        $walletCount = 3;

        for ($i = 0; $i < $walletCount; ++$i) {
            $wallet = new Wallet();
            $wallet->setTitle($this->faker->sentence);
            $wallet->setDescription($this->faker->text(200));
            $wallet->setBalance($this->faker->randomFloat(2, 0, 10000));
            $wallet->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $wallet->setUpdatedAt($this->faker->dateTimeBetween('-100 days', 'now'));
            $wallet->setTags(implode(', ', $this->faker->words(3)));
            $this->manager->persist($wallet);

            $this->addReference('wallet_' . $i, $wallet);
        }

        $this->manager->flush();
    }
}
