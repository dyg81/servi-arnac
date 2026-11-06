<?php

namespace App\Repository;

use App\Entity\Vitrina;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vitrina>
 *
 * @method Vitrina|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vitrina|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vitrina[]    findAll()
 * @method Vitrina[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VitrinaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vitrina::class);
    }

    public function obtenerVitrinasVencidas()
    {
        return $this->createQueryBuilder('v')
            ->where("date_format(v.fecha_vencimiento, '%Y-%m-%d') <= :hoy")
            ->setParameter('hoy', date_format(new DateTime('today'), 'Y-m-d'))
            ->getQuery()
            ->getResult();
    }
}
