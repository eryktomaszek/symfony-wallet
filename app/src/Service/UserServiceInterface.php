<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Service;

use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface UserServiceInterface.
 */
interface UserServiceInterface
{
    /**
     * Get paginated list of users.
     *
     * @param int $page The page number for pagination
     *
     * @return PaginationInterface The paginated list of users
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save a user entity.
     *
     * @param User $user The user entity to save
     */
    public function save(User $user): void;

    /**
     * Delete a user entity.
     *
     * @param User $user The user entity to delete
     */
    public function delete(User $user): void;
}
