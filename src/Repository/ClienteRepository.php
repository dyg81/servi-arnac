<?php

namespace App\Repository;

use App\Entity\Carta;
use App\Entity\Cliente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cliente>
 *
 * @method Cliente|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cliente|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cliente[]    findAll()
 * @method Cliente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClienteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cliente::class);
    }

    public function obtenerClientesAutorizados()
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.cartas', 'carta')
            ->where('carta.estado = :carta_estado')
            ->setParameter("carta_estado", Carta::CARTA_APROBADA)
            ->getQuery()
            ->getResult();
    }
}
