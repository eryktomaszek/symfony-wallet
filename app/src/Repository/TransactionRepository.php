<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

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
     * TransactionRepository constructor.
     *
     * @param ManagerRegistry $registry The manager registry for Doctrine
     */
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
            ->select('partial transaction.{id, amount, description, date, type, balanceAfter}', 'partial category.{id, name}', 'partial tags.{id, title}')
            ->join('transaction.category', 'category')
            ->leftJoin('transaction.tags', 'tags')
            ->orderBy('transaction.date', 'ASC');
    }

    /**
     * Count the number of transactions associated with a category.
     *
     * @param Category $category The category entity
     *
     * @return int The number of transactions for the category
     */
    public function countByCategory(Category $category): int
    {
        try {
            return (int) $this->createQueryBuilder('transaction')
                ->select('COUNT(transaction.id)')
                ->where('transaction.category = :category')
                ->setParameter('category', $category)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException) {
            return 0;
        }
    }

    /**
     * Save a transaction entity.
     *
     * @param Transaction $entity The transaction entity
     * @param bool        $flush  Whether to flush the changes (default: false)
     */
    public function save(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove a transaction entity.
     *
     * @param Transaction $entity The transaction entity
     * @param bool        $flush  Whether to flush the changes (default: false)
     */
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
     * @param User                    $user      The user (author of the transactions)
     * @param \DateTimeInterface|null $startDate The start date for filtering (optional)
     * @param \DateTimeInterface|null $endDate   The end date for filtering (optional)
     * @param Category|null           $category  The category for filtering (optional)
     * @param array                   $tags      The tags for filtering (optional)
     *
     * @return QueryBuilder Query builder
     */
    public function queryByAuthorAndFilters(User $user, ?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null, ?Category $category = null, array $tags = []): QueryBuilder
    {
        $qb = $this->queryAll()
            ->andWhere('transaction.author = :author')
            ->setParameter('author', $user);

        if ($startDate instanceof \DateTimeInterface) {
            $qb->andWhere('transaction.date >= :startDate')
                ->setParameter('startDate', $startDate->format('Y-m-d'));
        }

        if ($endDate instanceof \DateTimeInterface) {
            $qb->andWhere('transaction.date <= :endDate')
                ->setParameter('endDate', $endDate->format('Y-m-d 23:59:59'));
        }

        if ($category instanceof Category) {
            $qb->andWhere('transaction.category = :category')
                ->setParameter('category', $category);
        }

        if ([] !== $tags) {
            $qb->andWhere('tags.id IN (:tags)')
                ->setParameter('tags', $tags);
        }

        return $qb;
    }

    /**
     * Retrieves a QueryBuilder for filtering transactions based on the user, category, tags, start date, and end date.
     *
     * This method constructs a Doctrine QueryBuilder to filter transactions for a given user. Optional filters for category,
     * tags, start date, and end date are applied if provided. If no filters are provided, all transactions for the user
     * are returned.
     *
     * @param User                    $user       the user for whom transactions are being filtered
     * @param int|null                $categoryId the optional category ID to filter transactions by
     * @param array                   $tags       an optional array of tag IDs to filter transactions by
     * @param \DateTimeInterface|null $startDate  the optional start date to filter transactions from
     * @param \DateTimeInterface|null $endDate    the optional end date to filter transactions up to
     *
     * @return QueryBuilder the QueryBuilder object containing the constructed query for filtered transactions
     */
    public function findByFiltersQuery(User $user, ?int $categoryId = null, array $tags = [], ?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('transaction')
            ->andWhere('transaction.author = :author')
            ->setParameter('author', $user);

        if ($categoryId) {
            $qb->andWhere('transaction.category = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        if ([] !== $tags) {
            $qb->join('transaction.tags', 't')
                ->andWhere('t.id IN (:tags)')
                ->setParameter('tags', $tags);
        }

        if ($startDate instanceof \DateTimeInterface) {
            $qb->andWhere('transaction.date >= :startDate')
                ->setParameter('startDate', $startDate->format('Y-m-d'));
        }

        if ($endDate instanceof \DateTimeInterface) {
            $qb->andWhere('transaction.date <= :endDate')
                ->setParameter('endDate', $endDate->format('Y-m-d 23:59:59'));
        }

        return $qb;
    }
}
