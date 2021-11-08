<?php

namespace App\Repository;

use App\Entity\Estante;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Estante|null find($id, $lockMode = null, $lockVersion = null)
 * @method Estante|null findOneBy(array $criteria, array $orderBy = null)
 * @method Estante[]    findAll()
 * @method Estante[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstanteRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Estante::class);
    }

    /**
     * @return Estante[]
     */
    public function findAllOrderByAsc()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.numero', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Estante[] Returns an array of Estante objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Estante
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
