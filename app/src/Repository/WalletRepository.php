<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Repository;

use App\Entity\Wallet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wallet>
 */
class WalletRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wallet::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select('partial wallet.{id, title, description, balance}')
            ->orderBy('wallet.id', 'ASC');
    }

    /**
     * Save entity.
     *
     * @param Wallet $entity Wallet entity
     * @param bool   $flush  Whether to flush changes to the database
     */
    public function save(Wallet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove entity.
     *
     * @param Wallet $entity Wallet entity
     * @param bool   $flush  Whether to flush changes to the database
     */
    public function remove(Wallet $entity, bool $flush = false): void
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
        return $this->createQueryBuilder('wallet');
    }
}
