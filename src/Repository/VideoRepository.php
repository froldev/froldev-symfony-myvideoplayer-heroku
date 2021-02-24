<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }

    /**
     * @return Video[] Returns an array of Video objects
     */
    public function findVideoBySearch(string $value)
    {
        $queryBuilder = $this->createQueryBuilder("v")
            ->join("v.category", "c")
            ->addSelect("c")
            ->where("c.name LIKE :value")
            ->orWhere("v.name LIKE :value")
            ->orWhere("v.author LIKE :value")
            ->setParameter("value", "%" . $value . "%")
            ->orderBy("v.name", "ASC")
            ->getQuery();
        return $queryBuilder->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Video
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter("val", "%".$value."%")
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
