<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class TagService.
 */
class TagService implements TagServiceInterface
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
     * @param TagRepository      $tagRepository Tag repository
     * @param PaginatorInterface $paginator     Paginator
     */
    public function __construct(private readonly TagRepository $tagRepository, private readonly PaginatorInterface $paginator)
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
            $this->tagRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save tag.
     *
     * @param Tag $tag Tag entity
     */
    public function save(Tag $tag): void
    {
        $this->tagRepository->save($tag, true);
    }

    /**
     * Delete tag.
     *
     * @param Tag $tag Tag entity
     */
    public function delete(Tag $tag): void
    {
        $this->tagRepository->remove($tag, true);
    }

    /**
     * Get all tags.
     *
     * @return array<Tag> List of tags
     */
    public function getAllTags(): array
    {
        return $this->tagRepository->findAll();
    }
}
