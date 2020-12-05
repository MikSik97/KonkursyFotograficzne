<?php

namespace App\Repository;

use App\Entity\Contestants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contestants|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contestants|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contestants[]    findAll()
 * @method Contestants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContestantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contestants::class);
    }

    // /**
    //  * @return Contestants[] Returns an array of Contestants objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Contestants
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
