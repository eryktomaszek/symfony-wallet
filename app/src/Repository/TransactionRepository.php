<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     *
     * @constant int
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 5;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select(
                'partial transaction.{id, date, type, amount, description}',
                'partial category.{id, name}'
            )
            ->join('transaction.category', 'category')
            ->orderBy('transaction.id', 'ASC');
    }

    /**
     * Save entity.
     *
     * @param Transaction $entity Transaction entity
     * @param bool        $flush  Whether to flush changes to the database
     */
    public function save(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove entity.
     *
     * @param Transaction $entity Transaction entity
     * @param bool        $flush  Whether to flush changes to the database
     */
    public function remove(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Count transactions by category.
     *
     * @param Category $category Category entity
     *
     * @return int Number of transactions in category
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByCategory(Category $category): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return (int) $qb->select($qb->expr()->countDistinct('transaction.id'))
            ->where('transaction.category = :category')
            ->setParameter(':category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param User $user
     * @return QueryBuilder
     */
    public function queryByAuthor(User $user): QueryBuilder
    {
        return $this->queryAll()
            ->andWhere('transaction.author = :author')
            ->setParameter('author', $user);
    }


    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('transaction');
    }
}
