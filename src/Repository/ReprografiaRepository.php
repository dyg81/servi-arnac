<?php

namespace App\Repository;

use App\Entity\Reprografia;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reprografia>
 *
 * @method Reprografia|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reprografia|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reprografia[]    findAll()
 * @method Reprografia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReprografiaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reprografia::class);
    }

    /**
     * @return Reprografia[] Returns an array of Reprografia objects
     */
    public function obtenerReprografiasPorFechaActualizacion(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.fecha_actualizacion', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Reprografia[] Returns an array of Reprografia objects
     */
    public function obtenerReprografiasSolicitadas(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.estado = :estado_solicitado')
            ->setParameter('estado_solicitado', Reprografia::ESTADO_SOLICITADO)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Reprografia[] Returns an array of Reprografia objects
     */
    public function obtenerReprografiasNoCompletadas(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.estado = :estado_solicitado')
            ->orWhere('r.estado = :estado_en_proceso')
            ->setParameter('estado_solicitado', Reprografia::ESTADO_SOLICITADO)
            ->setParameter('estado_en_proceso', Reprografia::ESTADO_EN_PROCESO)
            ->orderBy('r.fecha_actualizacion', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Reprografia[] Returns an array of Reprografia objects
     */
    public function obtenerReprografiasCompletadas(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.estado = :estado_terminado')
            ->orWhere('r.estado = :estado_cancelado')
            ->setParameter('estado_terminado', Reprografia::ESTADO_TERMINADO)
            ->setParameter('estado_cancelado', Reprografia::ESTADO_CANCELADO)
            ->orderBy('r.fecha_actualizacion', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Reprografia[] Returns an array of Reprografia objects
     */
    public function obtenerReprografiasPorFechaYCliente($fecha, $clienteid): array
    {
        return $this->createQueryBuilder('r')
            ->where("date_format(r.fecha_solicitud, '%Y-%m-%d') = :fecha")
            ->andWhere('r.cliente = :clienteid')
            ->setParameter('fecha', date_format($fecha, 'Y-m-d'))
            ->setParameter('clienteid', $clienteid)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Reprografia[] Returns an array of Reprografia objects
     */
    public function obtenerReprografiasPorDia($fecha): array
    {
        return $this->createQueryBuilder('r')
            ->where("date_format(r.fecha_solicitud, '%Y-%m-%d') = :fecha")
            ->setParameter('fecha', date_format($fecha, 'Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Reprografia[] Returns an array of Reprografia objects
     */
    public function obtenerReprografiasDelDia(): array
    {
        return $this->createQueryBuilder('r')
            ->where("date_format(r.fecha_solicitud, '%Y-%m-%d') = :hoy")
            ->setParameter('hoy', date_format(new DateTime('today'), 'Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Reprografia[] Returns an array of Reprografia objects
     */
    public function obtenerReprografiasPorMes($mes_inicial): array
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

    public function contarPorMes($mes_inicial): int
    {
        $mes_posterior = clone $mes_inicial;

        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.fecha_solicitud >= :mes_inicial')
            ->andWhere('r.fecha_solicitud < :mes_posterior')
            ->setParameter('mes_inicial', $mes_inicial)
            ->setParameter('mes_posterior', DATE_ADD($mes_posterior, DateInterval::createFromDateString('1 month')))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
