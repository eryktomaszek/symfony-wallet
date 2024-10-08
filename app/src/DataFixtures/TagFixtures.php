<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\DataFixtures;

use App\Entity\Tag;

/**
 * Class TagFixtures.
 */
class TagFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     */
    protected function loadData(): void
    {
        for ($i = 0; $i < 5; ++$i) {
            $tag = new Tag();
            $tag->setTitle($this->faker->unique()->word);
            $this->manager->persist($tag);
        }

        $this->manager->flush();
    }
}
