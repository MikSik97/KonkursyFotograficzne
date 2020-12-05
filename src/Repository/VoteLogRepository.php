<?php

namespace App\Repository;

use App\Entity\VoteLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VoteLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method VoteLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method VoteLog[]    findAll()
 * @method VoteLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoteLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VoteLog::class);
    }

    // /**
    //  * @return VoteLog[] Returns an array of VoteLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VoteLog
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
