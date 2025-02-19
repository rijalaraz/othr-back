<?php

namespace App\Repository;

use App\Entity\NetworkType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method NetworkType|null find($id, $lockMode = null, $lockVersion = null)
 * @method NetworkType|null findOneBy(array $criteria, array $orderBy = null)
 * @method NetworkType[]    findAll()
 * @method NetworkType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NetworkTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NetworkType::class);
    }

    // /**
    //  * @return NetworkType[] Returns an array of NetworkType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NetworkType
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
