<?php

namespace App\Repository;

use App\Entity\Fondo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fondo>
 *
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

    /**
     * @return Fondo[] Returns an array of Fondo objects
     */
    public function obtenerFondosMasUsados(): array
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.contador', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }
}
