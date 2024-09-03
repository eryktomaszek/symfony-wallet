<?php

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
 */
class TransactionService implements TransactionServiceInterface
{
    /**
     * Items per page.
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 5;

    private TransactionRepository $transactionRepository;
    private PaginatorInterface $paginator;
    private ValidatorInterface $validator;
    private TranslatorInterface $translator;

    /**
     * Constructor.
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        PaginatorInterface $paginator,
        ValidatorInterface $validator,
        TranslatorInterface $translator,
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->paginator = $paginator;
        $this->validator = $validator;
        $this->translator = $translator;
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, User $author): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->transactionRepository->queryByAuthor($author),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save transaction.
     */
    public function save(Transaction $transaction): void
    {
        $errors = $this->validator->validate($transaction);
        if (count($errors) > 0) {
            throw new ValidationFailedException($transaction, $errors);
        }

        $wallet = $transaction->getWallet();
        $newBalance = $wallet->getBalance();

        if ('expense' === $transaction->getType()) {
            $newBalance -= $transaction->getAmount();
        } else {
            $newBalance += $transaction->getAmount();
        }

        if ($newBalance < 0) {
            $errorMessage = $this->translator->trans('wallet.balance_error');
            throw new \InvalidArgumentException($errorMessage);
        }

        $wallet->setBalance($newBalance);

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
