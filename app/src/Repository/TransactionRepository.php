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
     * Query transactions by author and optional date range.
     *
     * @param User $user
     * @param \DateTimeInterface|null $startDate
     * @param \DateTimeInterface|null $endDate
     *
     * @return QueryBuilder
     */
    public function queryByAuthorAndDateRange(User $user, ?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null): QueryBuilder
    {
        $qb = $this->queryAll()
            ->andWhere('transaction.author = :author')
            ->setParameter('author', $user);

        if ($startDate) {
            $qb->andWhere('transaction.date >= :startDate')
                ->setParameter('startDate', $startDate->format('Y-m-d'));
        }

        if ($endDate) {
            $qb->andWhere('transaction.date <= :endDate')
                ->setParameter('endDate', $endDate->format('Y-m-d'));
        }

        return $qb;
    }
}
