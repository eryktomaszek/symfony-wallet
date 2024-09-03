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

class TransactionService implements TransactionServiceInterface
{
    private const PAGINATOR_ITEMS_PER_PAGE = 5;

    private TransactionRepository $transactionRepository;
    private PaginatorInterface $paginator;
    private ValidatorInterface $validator;
    private TranslatorInterface $translator;

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

    public function getPaginatedList(int $page, User $user, ?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null): PaginationInterface
    {
        $queryBuilder = $this->transactionRepository->queryByAuthorAndDateRange($user, $startDate, $endDate);
        return $this->paginator->paginate($queryBuilder, $page, self::PAGINATOR_ITEMS_PER_PAGE);
    }

    public function save(Transaction $transaction): void
    {
        // Validate the transaction entity
        $errors = $this->validator->validate($transaction);
        if (count($errors) > 0) {
            throw new ValidationFailedException($transaction, $errors);
        }

        // Adjust wallet balance logic
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

        // Save the transaction
        $this->transactionRepository->save($transaction, true);
    }


    public function delete(Transaction $transaction): void
    {
        $this->transactionRepository->remove($transaction, true);
    }
}
