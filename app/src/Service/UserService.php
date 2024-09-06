<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class UserService.
 *
 * Provides methods for managing users, including pagination, saving, and deletion.
 */
class UserService implements UserServiceInterface
{
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * UserService constructor.
     *
     * @param UserRepository     $userRepository The user repository for database operations
     * @param PaginatorInterface $paginator      The paginator for handling pagination
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list of users.
     *
     * @param int $page The page number for pagination
     *
     * @return PaginationInterface The paginated list of users
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->userRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save a user entity.
     *
     * @param User $user The user entity to save
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user, true);
    }

    /**
     * Delete a user entity.
     *
     * @param User $user The user entity to delete
     */
    public function delete(User $user): void
    {
        $this->userRepository->remove($user, true);
    }
}
