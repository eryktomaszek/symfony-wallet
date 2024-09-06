<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CategoryService.
 */
class CategoryService implements CategoryServiceInterface
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
     * @param CategoryRepository    $categoryRepository    Category repository
     * @param PaginatorInterface    $paginator             Paginator
     * @param TransactionRepository $transactionRepository Transaction repository
     */
    public function __construct(private readonly CategoryRepository $categoryRepository, private readonly PaginatorInterface $paginator, private readonly TransactionRepository $transactionRepository)
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
            $this->categoryRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save category.
     *
     * @param Category $category Category entity
     */
    public function save(Category $category): void
    {
        $this->categoryRepository->save($category, true);
    }

    /**
     * Delete category.
     *
     * @param Category $category Category entity
     */
    public function delete(Category $category): void
    {
        $this->categoryRepository->remove($category, true);
    }

    /**
     * Can Category be deleted?
     *
     * @param Category $category Category entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Category $category): bool
    {
        try {
            $result = $this->transactionRepository->countByCategory($category);

            return 0 === $result;
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }

    /**
     * Get all categories.
     *
     * @return array<Category> List of categories
     */
    public function getAllCategories(): array
    {
        return $this->categoryRepository->findAll();
    }
}
