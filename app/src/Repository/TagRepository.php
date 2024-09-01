<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 */
class TagRepository extends ServiceEntityRepository
{
    /**
     * @param string $entityClass The class name of the entity this repository manages
     * @psalm-param class-string<T> $entityClass
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * Query all tags.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('tag')
            ->orderBy('tag.id', 'ASC');
    }

    /**
     * Save entity.
     *
     * @param Tag  $entity Tag entity
     * @param bool $flush  Whether to flush the changes (default: false)
     */
    public function save(Tag $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove entity.
     *
     * @param Tag  $entity Tag entity
     * @param bool $flush  Whether to flush the changes (default: false)
     */
    public function remove(Tag $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
