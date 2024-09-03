<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\Category;
use App\Repository\TransactionRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TransactionService.
 */
class TransactionService implements TransactionServiceInterface
{
    private const PAGINATOR_ITEMS_PER_PAGE = 5;

    private TransactionRepository $transactionRepository;
    private PaginatorInterface $paginator;
    private ValidatorInterface $validator;
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param TransactionRepository $transactionRepository Transaction repository
     * @param PaginatorInterface    $paginator             Paginator
     * @param ValidatorInterface    $validator             Validator
     * @param TranslatorInterface   $translator            Translator
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        PaginatorInterface $paginator,
        ValidatorInterface $validator,
        TranslatorInterface $translator
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->paginator = $paginator;
        $this->validator = $validator;
        $this->translator = $translator;
    }

    /**
     * Get paginated list of transactions.
     *
     * @param int                    $page      Page number
     * @param User                   $user      User entity
     * @param \DateTimeInterface|null $startDate Start date filter
     * @param \DateTimeInterface|null $endDate   End date filter
     * @param Category|null           $category  Category entity filter
     *
     * @return PaginationInterface Paginated list of transactions
     */
    public function getPaginatedList(
        int $page,
        User $user,
        ?\DateTimeInterface $startDate = null,
        ?\DateTimeInterface $endDate = null,
        ?Category $category = null,
        array $tags = []
    ): PaginationInterface {
        $queryBuilder = $this->transactionRepository->queryByAuthorAndFilters($user, $startDate, $endDate, $category, $tags);
        return $this->paginator->paginate($queryBuilder, $page, self::PAGINATOR_ITEMS_PER_PAGE);
    }

    /**
     * Save transaction.
     *
     * @param Transaction $transaction Transaction entity
     *
     * @throws ValidationFailedException
     * @throws \InvalidArgumentException
     */
    public function save(Transaction $transaction): void
    {
        // If the transaction is new (has no id), calculate the new balance and set balanceAfter
        if ($transaction->getId() === null) {
            $wallet = $transaction->getWallet();
            $currentBalance = $wallet->getBalance();

            // Calculate the new balance after this transaction
            $newBalance = $currentBalance + ($transaction->getType() === 'income' ? $transaction->getAmount() : -$transaction->getAmount());

            if ($newBalance < 0) {
                $errorMessage = $this->translator->trans('wallet.balance_error');
                throw new \InvalidArgumentException($errorMessage);
            }

            // Update the transaction's balanceAfter field
            $transaction->setBalanceAfter($newBalance);

            // Update the wallet's balance
            $wallet->setBalance($newBalance);
        }

        // Validate the transaction
        $errors = $this->validator->validate($transaction);
        if (count($errors) > 0) {
            throw new ValidationFailedException($transaction, $errors);
        }

        // Persist the transaction
        $this->transactionRepository->save($transaction, true);
    }

    /**
     * Delete transaction.
     *
     * @param Transaction $transaction Transaction entity
     */
    public function delete(Transaction $transaction): void
    {
        $this->transactionRepository->remove($transaction, true);
    }
}
