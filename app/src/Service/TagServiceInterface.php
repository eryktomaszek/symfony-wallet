<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Service;

use App\Entity\Tag;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface TagServiceInterface.
 */
interface TagServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save tag.
     *
     * @param Tag $tag Tag entity
     */
    public function save(Tag $tag): void;

    /**
     * Delete tag.
     *
     * @param Tag $tag Tag entity
     */
    public function delete(Tag $tag): void;

    /**
     * Get all tags.
     *
     * @return array<Tag> List of tags
     */
    public function getAllTags(): array;
}
