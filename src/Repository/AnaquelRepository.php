<?php

namespace App\Repository;

use App\Entity\Anaquel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Anaquel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Anaquel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Anaquel[]    findAll()
 * @method Anaquel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnaquelRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Anaquel::class);
    }

    /**
     * @return Anaquel[]
     */
    public function findAllOrderByAsc()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.numero', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Anaquel[] Returns an array of Anaquel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Anaquel
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
