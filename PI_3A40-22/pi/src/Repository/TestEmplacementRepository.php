<?php

namespace App\Repository;

use App\Entity\TestEmplacement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TestEmplacement|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestEmplacement|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestEmplacement[]    findAll()
 * @method TestEmplacement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestEmplacementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestEmplacement::class);
    }

    // /**
    //  * @return TestEmplacement[] Returns an array of TestEmplacement objects
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
    public function findOneBySomeField($value): ?TestEmplacement
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
