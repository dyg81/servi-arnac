<?php

namespace App\Repository;

use App\Entity\Transcripcion;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transcripcion>
 *
 * @method Transcripcion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transcripcion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transcripcion[]    findAll()
 * @method Transcripcion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TranscripcionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transcripcion::class);
    }

    /**
     * @return Transcripcion[] Returns an array of Transcripcion objects
     */
    public function obtenerTranscripcionesPorFechaActualizacion(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.fecha_actualizacion', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Transcripcion[] Returns an array of Transcripcion objects
     */
    public function obtenerTranscripcionesSolicitadas(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.estado = :estado_solicitado')
            ->setParameter('estado_solicitado', Transcripcion::ESTADO_SOLICITADO)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Transcripcion[] Returns an array of Transcripcion objects
     */
    public function obtenerTranscripcionesNoCompletadas(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.estado = :estado_solicitado')
            ->orWhere('t.estado = :estado_en_proceso')
            ->setParameter('estado_solicitado', Transcripcion::ESTADO_SOLICITADO)
            ->setParameter('estado_en_proceso', Transcripcion::ESTADO_EN_PROCESO)
            ->orderBy('t.fecha_actualizacion', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Transcripcion[] Returns an array of Transcripcion objects
     */
    public function obtenerTranscripcionesCompletadas(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.estado = :estado_terminado')
            ->orWhere('t.estado = :estado_cancelado')
            ->setParameter('estado_terminado', Transcripcion::ESTADO_TERMINADO)
            ->setParameter('estado_cancelado', Transcripcion::ESTADO_CANCELADO)
            ->orderBy('t.fecha_actualizacion', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Transcripcion[] Returns an array of Transcripcion objects
     */
    public function obtenerTranscripcionesPorFechaYCliente($fecha, $clienteid): array
    {
        return $this->createQueryBuilder('t')
            ->where("date_format(t.fecha_solicitud, '%Y-%m-%d') = :fecha")
            ->andWhere('t.cliente = :clienteid')
            ->setParameter('fecha', date_format($fecha, 'Y-m-d'))
            ->setParameter('clienteid', $clienteid)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Transcripcion[] Returns an array of Transcripcion objects
     */
    public function obtenerTranscripcionesPorDia($fecha): array
    {
        return $this->createQueryBuilder('t')
            ->where("date_format(t.fecha_solicitud, '%Y-%m-%d') = :fecha")
            ->setParameter('fecha', date_format($fecha, 'Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Transcripcion[] Returns an array of Transcripcion objects
     */
    public function obtenerTranscripcionesDelDia(): array
    {
        return $this->createQueryBuilder('t')
            ->where("date_format(t.fecha_solicitud, '%Y-%m-%d') = :hoy")
            ->setParameter('hoy', date_format(new DateTime('today'), 'Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Transcripcion[] Returns an array of Transcripcion objects
     */
    public function obtenerTranscripcionesPorMes($mes_inicial): array
    {
        $mes_posterior = clone $mes_inicial;

        return $this->createQueryBuilder('t')
            ->where('t.fecha_solicitud >= :mes_inicial')
            ->andWhere('t.fecha_solicitud < :mes_posterior')
            ->setParameter('mes_inicial', $mes_inicial)
            ->setParameter('mes_posterior', DATE_ADD($mes_posterior, DateInterval::createFromDateString('1 month')))
            ->getQuery()
            ->getResult();
    }
}
