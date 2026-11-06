<?php

namespace App\Repository;

use App\Entity\Legajo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Legajo>
 *
 * @method Legajo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Legajo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Legajo[]    findAll()
 * @method Legajo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LegajoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Legajo::class);
    }
}
