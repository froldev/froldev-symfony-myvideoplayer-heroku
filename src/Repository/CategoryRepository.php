<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\Cast\Array_;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function countCategories(): int
    {
        return $this
            ->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Category[] Returns an array of User objects
     */
    public function findCategoryBySearch(string $value): array
    {
        $queryBuilder = $this->createQueryBuilder("c")
            ->where("c.name LIKE :value")
            ->setParameter("value", "%" . $value . "%")
            ->orderBy("c.position", "ASC")
            ->getQuery();
        return $queryBuilder->getResult();
    }

    /**
     * function to find videos by category
     * @param Category $category
     * @param string $value
     * @return Array
     */
    public function findVideoByCategoryAndSearch(Category $category, string $value)
    {
        $queryBuilder = $this->createQueryBuilder("c")
            ->join("c.videos", "v")
            ->addSelect("v")
            ->select("v.name", "v.url", "c.slug")
            ->where("v.name LIKE :value")
            ->andWhere("c.name = :name")
            ->setParameter("value", "%" . $value . "%")
            ->setParameter("name", $category->getName())
            ->getQuery();
        return $queryBuilder->getResult();
    }


    public function getIdByPosition(int $position)
    {
        return $this->createQueryBuilder("c")
            ->select("c.id")
            ->where("c.position = :position")
            ->setParameter("position", $position)
            ->getQuery()
            ->getSingleResult();
    }
}
