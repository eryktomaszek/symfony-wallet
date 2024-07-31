<?php

namespace App\DataFixtures;

use App\Entity\Category;

/**
 * Class CategoryFixtures.
 */
class CategoryFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     */
    protected function loadData(): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $category = new Category();
            $category->setName($this->faker->unique()->word);
            $category->setDescription($this->faker->text(200));
            $category->setCreatedAt($this->faker->dateTimeThisDecade);
            $category->setUpdatedAt($this->faker->dateTimeThisDecade);

            $this->manager->persist($category);
            $this->addReference('category_' . $i, $category);
        }

        $this->manager->flush();
    }
}

