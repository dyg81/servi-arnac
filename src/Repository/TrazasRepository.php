<?php

namespace App\Repository;

use App\Entity\Traza;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Traza>
 *
 * @method Traza|null find($id, $lockMode = null, $lockVersion = null)
 * @method Traza|null findOneBy(array $criteria, array $orderBy = null)
 * @method Traza[]    findAll()
 * @method Traza[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrazasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Traza::class);
    }

    /**
     * @return Traza[] Returns an array of Trazas objects
     */
    public function obtenerTrazasOrdenadasPorFecha(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.fecha', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
