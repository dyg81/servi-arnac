<?php

namespace App\Repository;

use App\Entity\Fondo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Fondo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fondo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fondo[]    findAll()
 * @method Fondo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FondoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fondo::class);
    }

    // /**
    //  * @return Fondo[] Returns an array of Fondo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Fondo
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
