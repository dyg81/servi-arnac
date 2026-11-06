<?php

namespace App\Repository;

use App\Entity\Deposito;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Deposito>
 *
 * @method Deposito|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deposito|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deposito[]    findAll()
 * @method Deposito[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepositoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deposito::class);
    }

    /**
     * @param $fondo_id
     * @return Deposito[] Returns an array of Deposito objects
     */
    public function obtenerDepositosPorFondo($fondo_id): array
    {
        return $this->createQueryBuilder('d')
            ->innerJoin('d.fondos', 'fondo', 'WITH', 'fondo.id = :fondoid')
            ->setParameter("fondoid", $fondo_id)
            ->orderBy('d.numero', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
