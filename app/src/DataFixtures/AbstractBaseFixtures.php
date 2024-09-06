<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

/**
 * Class AbstractBaseFixtures.
 */
abstract class AbstractBaseFixtures extends Fixture
{
    /**
     * Faker generator instance.
     */
    protected Generator $faker;

    /**
     * Persistence object manager.
     */
    protected ObjectManager $manager;

    /**
     * Load.
     *
     * @param ObjectManager $manager Persistence object manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Factory::create();
        $this->loadData();
    }

    /**
     * Load data.
     */
    abstract protected function loadData(): void;
}
