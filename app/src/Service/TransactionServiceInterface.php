<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface TransactionServiceInterface.
 *
 * Provides methods for managing transactions, including handling balance calculations.
 */
interface TransactionServiceInterface
{
    /**
     * Get paginated list of transactions for a specific user.
     *
     * @param int                     $page      The page number for pagination
     * @param User                    $user      The user to filter transactions by
     * @param \DateTimeInterface|null $startDate The start date filter for transactions (optional)
     * @param \DateTimeInterface|null $endDate   The end date filter for transactions (optional)
     *
     * @return PaginationInterface The paginated list of transactions
     */
    public function getPaginatedList(int $page, User $user, ?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null): PaginationInterface;

    /**
     * Save a transaction entity and update wallet balance.
     *
     * @param Transaction $transaction The transaction entity to save
     */
    public function save(Transaction $transaction): void;

    /**
     * Delete a transaction entity and adjust wallet balance.
     *
     * @param Transaction $transaction The transaction entity to delete
     */
    public function delete(Transaction $transaction): void;
}
