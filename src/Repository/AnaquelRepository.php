<?php

namespace App\Repository;

use App\Entity\Anaquel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Anaquel>
 *
 * @method Anaquel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Anaquel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Anaquel[]    findAll()
 * @method Anaquel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnaquelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Anaquel::class);
    }
}
