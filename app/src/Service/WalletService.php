<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Service;

use App\Entity\Wallet;
use App\Repository\WalletRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class WalletService.
 */
class WalletService implements WalletServiceInterface
{
    /**
     * Items per page.
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 5;

    /**
     * Constructor.
     *
     * @param WalletRepository    $walletRepository Wallet repository
     * @param PaginatorInterface  $paginator        Paginator
     * @param ValidatorInterface  $validator        Validator
     * @param TranslatorInterface $translator       Translator
     */
    public function __construct(private readonly WalletRepository $walletRepository, private readonly PaginatorInterface $paginator, private readonly ValidatorInterface $validator, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->walletRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save wallet.
     *
     * @param Wallet $wallet Wallet entity
     *
     * @throws ValidationFailedException if balance is less than zero
     */
    public function save(Wallet $wallet): void
    {
        $errors = $this->validator->validate($wallet);
        if (count($errors) > 0) {
            throw new ValidationFailedException($wallet, $errors);
        }

        if ($wallet->getBalance() < 0) {
            $errorMessage = $this->translator->trans('message.balance_error');
            throw new \InvalidArgumentException($errorMessage);
        }

        $this->walletRepository->save($wallet, true);
    }

    /**
     * Delete wallet.
     *
     * @param Wallet $wallet Wallet entity
     */
    public function delete(Wallet $wallet): void
    {
        $this->walletRepository->remove($wallet, true);
    }
}
