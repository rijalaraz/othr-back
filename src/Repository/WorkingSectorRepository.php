<?php

namespace App\Repository;

use App\Entity\WorkingSector;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WorkingSector|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkingSector|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkingSector[]    findAll()
 * @method WorkingSector[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkingSectorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkingSector::class);
    }

    // /**
    //  * @return WorkingSector[] Returns an array of WorkingSector objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WorkingSector
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
