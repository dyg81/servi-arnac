<?php

namespace App\Repository;

use App\Entity\Estante;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Estante>
 *
 * @method Estante|null find($id, $lockMode = null, $lockVersion = null)
 * @method Estante|null findOneBy(array $criteria, array $orderBy = null)
 * @method Estante[]    findAll()
 * @method Estante[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstanteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Estante::class);
    }
}
