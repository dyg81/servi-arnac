<?php

namespace App\Repository;

use App\Entity\Investigacion;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Investigacion>
 *
 * @method Investigacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Investigacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Investigacion[]    findAll()
 * @method Investigacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvestigacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Investigacion::class);
    }

    /**
     * @return Investigacion[] Returns an array of Investigacion objects
     */
    public function obtenerInvestigacionesPorFechaActualizacion(): array
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.fecha_actualizacion', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Investigacion[] Returns an array of Investigacion objects
     */
    public function obtenerInvestigacionesPorFechaYCliente($fecha, $clienteid): array
    {
        return $this->createQueryBuilder('i')
            ->where("date_format(i.fecha_solicitud, '%Y-%m-%d') = :fecha")
            ->andWhere('i.cliente = :clienteid')
            ->setParameter('fecha', date_format($fecha, 'Y-m-d'))
            ->setParameter('clienteid', $clienteid)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Investigacion[] Returns an array of Investigacion objects
     */
    public function obtenerInvestigacionesPorDia($fecha): array
    {
        return $this->createQueryBuilder('i')
            ->where("date_format(i.fecha_solicitud, '%Y-%m-%d') = :fecha")
            ->setParameter('fecha', date_format($fecha, 'Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Investigacion[] Returns an array of Investigacion objects
     */
    public function obtenerInvestigacionesDelDia(): array
    {
        return $this->createQueryBuilder('i')
            ->where("date_format(i.fecha_solicitud, '%Y-%m-%d') = :hoy")
            ->setParameter('hoy', date_format(new DateTime('today'), 'Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Investigacion[] Returns an array of Investigacion objects
     */
    public function obtenerInvestigacionesCompletadas(): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.estado != :estado_terminado')
            ->orWhere('i.estado != :estado_cancelado')
            ->setParameter('estado_terminado', Investigacion::ESTADO_TERMINADO)
            ->setParameter('estado_cancelado', Investigacion::ESTADO_CANCELADO)
            ->orderBy('i.fecha_solicitud', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Investigacion[] Returns an array of Investigacion objects
     */
    public function obtenerInvestigacionesSolicitadas(): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.estado = :estado_solicitado')
            ->setParameter('estado_solicitado', Investigacion::ESTADO_SOLICITADO)
            ->orderBy('i.fecha_solicitud', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Investigacion[] Returns an array of Investigacion objects
     */
    public function obtenerInvestigacionesProcesadas(): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.estado != :estado_solicitado')
            ->setParameter('estado_solicitado', Investigacion::ESTADO_SOLICITADO)
            ->orderBy('i.fecha_solicitud', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Investigacion[] Returns an array of Investigacion objects
     */
    public function obtenerInvestigacionesEstadoProcesadas(): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.estado = :estado_procesado')
            ->setParameter('estado_procesado', Investigacion::ESTADO_PROCESADO)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Investigacion[] Returns an array of Investigacion objects
     */
    public function obtenerInvestigacionesCanceladasHace5Min(): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.estado = :estado_cancelado')
            ->andWhere('i.fecha_actualizacion > :haceCincoMinutos')
            ->setParameter('estado_cancelado', Investigacion::ESTADO_CANCELADO)
            ->setParameter('haceCincoMinutos', DATE_SUB(new DateTime(), DateInterval::createFromDateString('5 minutes')))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Investigacion[] Returns an array of Investigacion objects
     */
    public function obtenerInvestigacionesPorMes($mes_inicial): array
    {
        $mes_posterior = clone $mes_inicial;

        return $this->createQueryBuilder('i')
            ->where('i.fecha_solicitud >= :mes_inicial')
            ->andWhere('i.fecha_solicitud < :mes_posterior')
            ->setParameter('mes_inicial', $mes_inicial)
            ->setParameter('mes_posterior', DATE_ADD($mes_posterior, DateInterval::createFromDateString('1 month')))
            ->getQuery()
            ->getResult();
    }

    public function contarPorMes($mes_inicial): int
    {
        $mes_posterior = clone $mes_inicial;

        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.fecha_solicitud >= :mes_inicial')
            ->andWhere('i.fecha_solicitud < :mes_posterior')
            ->setParameter('mes_inicial', $mes_inicial)
            ->setParameter('mes_posterior', DATE_ADD($mes_posterior, DateInterval::createFromDateString('1 month')))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
