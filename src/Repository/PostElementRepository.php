<?php

namespace App\Repository;

use App\Entity\PostElement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PostElement|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostElement|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostElement[]    findAll()
 * @method PostElement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostElementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostElement::class);
    }

    // /**
    //  * @return PostElement[] Returns an array of PostElement objects
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
    public function findOneBySomeField($value): ?PostElement
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
