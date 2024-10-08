<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\TransactionRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TransactionService.
 *
 * Service responsible for handling transactions, including balance calculations.
 */
class TransactionService implements TransactionServiceInterface
{
    private const PAGINATOR_ITEMS_PER_PAGE = 5;

    /**
     * Constructor.
     *
     * @param TransactionRepository $transactionRepository Transaction repository
     * @param PaginatorInterface    $paginator             Paginator
     * @param ValidatorInterface    $validator             Validator
     * @param TranslatorInterface   $translator            Translator
     */
    public function __construct(private readonly TransactionRepository $transactionRepository, private readonly PaginatorInterface $paginator, private readonly ValidatorInterface $validator, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Get paginated list of transactions for a user.
     *
     * @param int                     $page      Page number
     * @param User                    $user      User entity to filter by
     * @param \DateTimeInterface|null $startDate Start date filter (optional)
     * @param \DateTimeInterface|null $endDate   End date filter (optional)
     *
     * @return PaginationInterface Paginated list of transactions
     */
    public function getPaginatedList(int $page, User $user, ?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null): PaginationInterface
    {
        $queryBuilder = $this->transactionRepository->queryByAuthorAndFilters($user, $startDate, $endDate);

        return $this->paginator->paginate($queryBuilder, $page, self::PAGINATOR_ITEMS_PER_PAGE);
    }

    /**
     * Save transaction and update wallet balance.
     *
     * @param Transaction $transaction Transaction entity
     *
     * @throws ValidationFailedException
     * @throws \InvalidArgumentException
     */
    public function save(Transaction $transaction): void
    {
        $wallet = $transaction->getWallet();
        $currentBalance = $wallet->getBalance();

        $newBalance = $currentBalance + ('income' === $transaction->getType() ? $transaction->getAmount() : -$transaction->getAmount());

        if ($newBalance < 0) {
            $errorMessage = $this->translator->trans('wallet.balance_error');
            throw new \InvalidArgumentException($errorMessage);
        }

        $transaction->setBalanceAfter($newBalance);
        $wallet->setBalance($newBalance);

        $errors = $this->validator->validate($transaction);
        if (count($errors) > 0) {
            throw new ValidationFailedException($transaction, $errors);
        }

        $this->transactionRepository->save($transaction, true);
    }

    /**
     * Delete transaction and adjust wallet balance.
     *
     * @param Transaction $transaction Transaction entity
     */
    public function delete(Transaction $transaction): void
    {
        $wallet = $transaction->getWallet();
        $currentBalance = $wallet->getBalance();

        $newBalance = $currentBalance - ('income' === $transaction->getType() ? $transaction->getAmount() : -$transaction->getAmount());

        $wallet->setBalance($newBalance);

        $this->transactionRepository->remove($transaction, true);
    }

    /**
     * Retrieves a QueryBuilder for filtered transactions based on the provided user, category, tags, start date, and end date.
     *
     * This method calls the transaction repository to build a query that filters transactions for a specific user.
     * Optional filters such as category, tags, start date, and end date can be applied.
     *
     * @param User                    $user       the user for whom the transactions are being filtered
     * @param int|null                $categoryId the optional category ID to filter transactions by
     * @param array                   $tags       an optional array of tag IDs to filter transactions by
     * @param \DateTimeInterface|null $startDate  the optional start date to filter transactions from
     * @param \DateTimeInterface|null $endDate    the optional end date to filter transactions up to
     *
     * @return \Doctrine\ORM\QueryBuilder the QueryBuilder object with the constructed query for filtered transactions
     */
    public function getFilteredTransactionsQuery(User $user, ?int $categoryId = null, array $tags = [], ?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null): \Doctrine\ORM\QueryBuilder
    {
        return $this->transactionRepository->findByFiltersQuery($user, $categoryId, $tags, $startDate, $endDate);
    }
}
