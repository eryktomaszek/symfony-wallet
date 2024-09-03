<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

interface TransactionServiceInterface
{
    /**
     * Get paginated list of transactions.
     *
     * @param int $page
     * @param User $user
     * @param \DateTimeInterface|null $startDate
     * @param \DateTimeInterface|null $endDate
     *
     * @return PaginationInterface
     */
    public function getPaginatedList(int $page, User $user, ?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null): PaginationInterface;

    /**
     * Save a transaction entity.
     *
     * @param Transaction $transaction
     */
    public function save(Transaction $transaction): void;

    /**
     * Delete a transaction entity.
     *
     * @param Transaction $transaction
     */
    public function delete(Transaction $transaction): void;
}
