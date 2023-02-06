<?php

namespace App\Repository;

use App\Entity\TestStock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TestStock|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestStock|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestStock[]    findAll()
 * @method TestStock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestStockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestStock::class);
    }

    // /**
    //  * @return TestStock[] Returns an array of TestStock objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TestStock
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
