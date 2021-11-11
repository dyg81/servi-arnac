<?php

namespace App\Repository;

use App\Entity\Deposito;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Deposito|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deposito|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deposito[]    findAll()
 * @method Deposito[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepositoRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deposito::class);
    }

    /**
     * @return Deposito[]
     */
    public function findAllOrderByAsc()
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.numero', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $fondo_id
     * @return Deposito[]
     */
    public function findAllByFondo($fondo_id)
    {
        return $this->createQueryBuilder("d")
            ->innerJoin('d.fondos', 'fondo', 'WITH', 'fondo.id = :fondoid')
            ->setParameter("fondoid", $fondo_id)
            ->orderBy('d.numero', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Deposito[] Returns an array of Deposito objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Deposito
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
