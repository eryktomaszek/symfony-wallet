<?php

namespace App\Service;

use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface UserServiceInterface.
 */
interface UserServiceInterface
{
    /**
     * Get paginated list.
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save user.
     */
    public function save(User $user): void;

    /**
     * Delete user.
     */
    public function delete(User $user): void;
}
