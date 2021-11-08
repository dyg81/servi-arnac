<?php

namespace App\Repository;

use App\Entity\Legajo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Legajo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Legajo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Legajo[]    findAll()
 * @method Legajo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LegajoRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Legajo::class);
    }

    /**
     * @return Legajo[]
     */
    public function findAllOrderByAsc()
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.legajo', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Legajo[] Returns an array of Legajo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Legajo
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
