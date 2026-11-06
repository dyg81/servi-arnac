<?php

namespace App\Repository;

use App\Entity\Certificacion;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Certificacion>
 *
 * @method Certificacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Certificacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Certificacion[]    findAll()
 * @method Certificacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CertificacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Certificacion::class);
    }

    /**
     * @return Certificacion[] Returns an array of Certificacion objects
     */
    public function obtenerCertificacionesPorFechaActualizacion(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.fecha_actualizacion', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Certificacion[] Returns an array of Certificacion objects
     */
    public function obtenerCertificacionesSolicitadas(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.estado = :estado_solicitado')
            ->setParameter('estado_solicitado', Certificacion::ESTADO_SOLICITADO)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Certificacion[] Returns an array of Certificacion objects
     */
    public function obtenerCertificacionesNoCompletadas(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.estado = :estado_solicitado')
            ->orWhere('c.estado = :estado_en_proceso')
            ->setParameter('estado_solicitado', Certificacion::ESTADO_SOLICITADO)
            ->setParameter('estado_en_proceso', Certificacion::ESTADO_EN_PROCESO)
            ->orderBy('c.fecha_actualizacion', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Certificacion[] Returns an array of Certificacion objects
     */
    public function obtenerCertificacionesCompletadas(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.estado = :estado_terminado')
            ->orWhere('c.estado = :estado_cancelado')
            ->setParameter('estado_terminado', Certificacion::ESTADO_TERMINADO)
            ->setParameter('estado_cancelado', Certificacion::ESTADO_CANCELADO)
            ->orderBy('c.fecha_actualizacion', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Certificacion[] Returns an array of Certificacion objects
     */
    public function obtenerCertificacionesPorFechaYCliente($fecha, $clienteid): array
    {
        return $this->createQueryBuilder('c')
            ->where("date_format(c.fecha_solicitud, '%Y-%m-%d') = :fecha")
            ->andWhere('c.cliente = :clienteid')
            ->setParameter('fecha', date_format($fecha, 'Y-m-d'))
            ->setParameter('clienteid', $clienteid)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Certificacion[] Returns an array of Certificacion objects
     */
    public function obtenerCertificacionesPorDia($fecha): array
    {
        return $this->createQueryBuilder('c')
            ->where("date_format(c.fecha_solicitud, '%Y-%m-%d') = :fecha")
            ->setParameter('fecha', date_format($fecha, 'Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Certificacion[] Returns an array of Certificacion objects
     */
    public function obtenerCertificacionesDelDia(): array
    {
        return $this->createQueryBuilder('c')
            ->where("date_format(c.fecha_solicitud, '%Y-%m-%d') = :hoy")
            ->setParameter('hoy', date_format(new DateTime('today'), 'Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Certificacion[] Returns an array of Certificacion objects
     */
    public function obtenerCertificacionesPorMes($mes_inicial): array
    {
        $mes_posterior = clone $mes_inicial;

        return $this->createQueryBuilder('r')
            ->where('r.fecha_solicitud >= :mes_inicial')
            ->andWhere('r.fecha_solicitud < :mes_posterior')
            ->setParameter('mes_inicial', $mes_inicial)
            ->setParameter('mes_posterior', DATE_ADD($mes_posterior, DateInterval::createFromDateString('1 month')))
            ->getQuery()
            ->getResult();
    }
}
