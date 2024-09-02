<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

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
        $tags = $this->manager->getRepository(Tag::class)->findAll();
        $users = $this->manager->getRepository(User::class)->findAll();

        for ($i = 0; $i < 10; ++$i) {
            $transaction = new Transaction();
            $transaction->setAmount($this->faker->randomFloat(2, 1, 1000));
            $transaction->setDescription($this->faker->text(200));
            $transaction->setDate($this->faker->dateTimeBetween('-100 days', 'now'));
            $transaction->setType($this->faker->randomElement(['income', 'expense']));
            $transaction->setWallet($this->getReference('wallet_'.$this->faker->numberBetween(0, 2)));
            $transaction->setCategory($this->getReference('category_'.$this->faker->numberBetween(0, 9)));

            /** @var User $author */
            $author = $this->faker->randomElement($users);
            $transaction->setAuthor($author);

            $randomTags = $this->faker->randomElements($tags, $this->faker->numberBetween(0, 3));
            foreach ($randomTags as $tag) {
                $transaction->addTag($tag);
            }

            $this->manager->persist($transaction);
        }

        $this->manager->flush();
    }

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
}
