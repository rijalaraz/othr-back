<?php

namespace App\Repository;

use App\Entity\NetworkMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method NetworkMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method NetworkMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method NetworkMember[]    findAll()
 * @method NetworkMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NetworkMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NetworkMember::class);
    }

    // /**
    //  * @return NetworkMember[] Returns an array of NetworkMember objects
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
    public function findOneBySomeField($value): ?NetworkMember
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
