<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry The manager registry for Doctrine connections
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Query all records with optimized field selection.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select(
                'partial category.{id, name, description}'
            )
            ->orderBy('category.id', 'ASC');
    }

    /**
     * Save entity.
     *
     * @param Category $entity Category entity
     * @param bool     $flush  Whether to flush changes to the database
     */
    public function save(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove entity.
     *
     * @param Category $entity Category entity
     * @param bool     $flush  Whether to flush changes to the database
     */
    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Get or create new query builder.
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('category');
    }
}
