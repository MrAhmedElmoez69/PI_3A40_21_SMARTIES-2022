<?php

namespace App\Repository;

use App\Entity\PieceDR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PieceDR|null find($id, $lockMode = null, $lockVersion = null)
 * @method PieceDR|null findOneBy(array $criteria, array $orderBy = null)
 * @method PieceDR[]    findAll()
 * @method PieceDR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PieceDRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PieceDR::class);
    }

    // /**
    //  * @return PieceDR[] Returns an array of PieceDR objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PieceDR
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
