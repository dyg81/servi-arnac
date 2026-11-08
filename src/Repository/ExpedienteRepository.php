<?php

namespace App\Repository;

use App\Entity\Expediente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Expediente>
 *
 * @method Expediente|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expediente|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expediente[]    findAll()
 * @method Expediente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpedienteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expediente::class);
    }

    /**
     * @param $fondoid
     * @param $legajoid
     * @return int|mixed|string
     */
    public function obtenerExpedientesPorFondoYLegajo($fondoid, $legajoid)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.legajo = :legajoid')
            ->andWhere('e.fondo = :fondoid')
            ->setParameter('legajoid', $legajoid)
            ->setParameter('fondoid', $fondoid)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $fondoid
     * @param $depositoid
     * @return int|mixed|string
     */
    public function obtenerExpedientesPorFondoYDeposito($fondoid, $depositoid)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.deposito = :depositoid')
            ->andWhere('e.fondo = :fondoid')
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
        return $this->createQueryBuilder('e')
            ->select('IDENTITY(e.fondo) AS fondo_id, IDENTITY(e.deposito) AS deposito_id, COUNT(e.id) AS cantidad')
            ->groupBy('e.fondo, e.deposito')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return Expediente[] Returns an array of Expediente objects
     */
    public function obtenerExpedientesPorArchivar(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.estado = :estado_sala_recepcion')
            ->orWhere('e.estado = :estado_transcripcion_terminada')
            ->orWhere('e.estado = :estado_reprografia_terminada')
            ->orWhere('e.estado = :estado_certificacion_terminada')
            ->setParameter('estado_sala_recepcion', Expediente::EXP_ESTADO_EN_SALA_RECEPCION)
            ->setParameter('estado_transcripcion_terminada', Expediente::EXP_ESTADO_TRANS_TERMINADA)
            ->setParameter('estado_reprografia_terminada', Expediente::EXP_ESTADO_DIG_TERMINADA)
            ->setParameter('estado_certificacion_terminada', Expediente::EXP_ESTADO_CERT_TERMINADA)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Expediente[] Returns an array of Expediente objects
     */
    public function obtenerExpedientesEnVitrina(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.estado = :vitrinas')
            ->setParameter('vitrinas', Expediente::EXP_ESTADO_EN_VITRINA)
            ->getQuery()
            ->getResult();
    }
}
