<?php

namespace App\Repository;

use App\Entity\Libro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Libro>
 *
 * @method Libro|null find($id, $lockMode = null, $lockVersion = null)
 * @method Libro|null findOneBy(array $criteria, array $orderBy = null)
 * @method Libro[]    findAll()
 * @method Libro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LibroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Libro::class);
    }

    /**
     * @param $fondoid
     * @return int|mixed|string
     */
    public function obtenerLibrosPorFondo($fondoid)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.fondo = :fondoid')
            ->setParameter('fondoid', $fondoid)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $fondoid
     * @param $depositoid
     * @return int|mixed|string
     */
    public function obtenerLibrosPorFondoYDeposito($fondoid, $depositoid)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.deposito = :depositoid')
            ->andWhere('l.fondo = :fondoid')
            ->setParameter('depositoid', $depositoid)
            ->setParameter('fondoid', $fondoid)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<int, array{fondo_id: string, deposito_id: string, cantidad: string}>
     */
    public function contarPorFondoYDeposito(): array
    {
        return $this->createQueryBuilder('l')
            ->select('IDENTITY(l.fondo) AS fondo_id, IDENTITY(l.deposito) AS deposito_id, COUNT(l.id) AS cantidad')
            ->groupBy('l.fondo, l.deposito')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return Libro[] Returns an array of Libro objects
     */
    public function obtenerLibrosPorArchivar(): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.estado = :estado_sala_recepcion')
            ->orWhere('l.estado = :estado_transcripcion_terminada')
            ->orWhere('l.estado = :estado_reprografia_terminada')
            ->orWhere('l.estado = :estado_certificacion_terminada')
            ->setParameter('estado_sala_recepcion', Libro::LIB_ESTADO_EN_SALA_RECEPCION)
            ->setParameter('estado_transcripcion_terminada', Libro::LIB_ESTADO_TRANS_TERMINADA)
            ->setParameter('estado_reprografia_terminada', Libro::LIB_ESTADO_DIG_TERMINADA)
            ->setParameter('estado_certificacion_terminada', Libro::LIB_ESTADO_CERT_TERMINADA)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Libro[] Returns an array of Libro objects
     */
    public function obtenerLibrosEnVitrina(): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.estado = :vitrina')
            ->setParameter('vitrina', Libro::LIB_ESTADO_EN_VITRINA)
            ->getQuery()
            ->getResult();
    }
}
