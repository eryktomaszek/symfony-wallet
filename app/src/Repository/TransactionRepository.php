<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository
{
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
        return $this->createQueryBuilder('transaction')
            ->select('transaction', 'category')
            ->join('transaction.category', 'category')
            ->orderBy('transaction.date', 'ASC');
    }

    public function save(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Query transactions by author, optional date range, and category.
     *
     * @param User $user
     * @param \DateTimeInterface|null $startDate
     * @param \DateTimeInterface|null $endDate
     * @param Category|null $category
     *
     * @return QueryBuilder
     */
    public function queryByAuthorAndFilters(User $user, ?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null, ?Category $category = null): QueryBuilder
    {
        $qb = $this->queryAll()
            ->andWhere('transaction.author = :author')
            ->setParameter('author', $user);

        if ($startDate) {
            $qb->andWhere('transaction.date >= :startDate')
                ->setParameter('startDate', $startDate->format('Y-m-d 00:00:00')); // Start of the day
        }

        if ($endDate) {
            $qb->andWhere('transaction.date <= :endDate')
                ->setParameter('endDate', $endDate->format('Y-m-d 23:59:59')); // End of the day
        }

        if ($category) {
            $qb->andWhere('transaction.category = :category')
                ->setParameter('category', $category);
        }

        return $qb;
    }
}

