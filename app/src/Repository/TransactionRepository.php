<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

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
            ->select('transaction', 'category', 'tags')
            ->join('transaction.category', 'category')
            ->leftJoin('transaction.tags', 'tags')
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
     */
    public function queryByAuthorAndFilters(
        User $user,
        ?\DateTimeInterface $startDate = null,
        ?\DateTimeInterface $endDate = null,
        ?Category $category = null,
        array $tags = [],
    ): QueryBuilder {
        $qb = $this->queryAll()
            ->andWhere('transaction.author = :author')
            ->setParameter('author', $user);

        if ($startDate) {
            $qb->andWhere('transaction.date >= :startDate')
                ->setParameter('startDate', $startDate->format('Y-m-d'));
        }

        if ($endDate) {
            $qb->andWhere('transaction.date <= :endDate')
                ->setParameter('endDate', $endDate->format('Y-m-d 23:59:59')); // Inclusive of the end date
        }

        if ($category) {
            $qb->andWhere('transaction.category = :category')
                ->setParameter('category', $category);
        }

        if (!empty($tags)) {
            $qb->andWhere('tags.id IN (:tags)')
                ->setParameter('tags', $tags);
        }

        return $qb;
    }
}
