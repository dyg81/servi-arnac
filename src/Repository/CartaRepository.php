<?php

namespace App\Repository;

use App\Entity\Carta;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Carta>
 *
 * @method Carta|null find($id, $lockMode = null, $lockVersion = null)
 * @method Carta|null findOneBy(array $criteria, array $orderBy = null)
 * @method Carta[]    findAll()
 * @method Carta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Carta::class);
    }

    /**
     * @return Carta[] Returns an array of Carta objects
     */
    public function obtenerCartasOrdenadasPorFechaSolicitud(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.fecha_solicitud', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Carta[] Returns an array of Carta objects
     */
    public function obtenerCartasSolicitadas(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.estado = :estado_solicitado')
            ->setParameter('estado_solicitado', Carta::CARTA_SOLICITADA)
            ->orderBy('c.fecha_solicitud', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Carta[] Returns an array of Carta objects
     */
    public function obtenerCartasRespondidas(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.estado != :estado_solicitado')
            ->setParameter('estado_solicitado', Carta::CARTA_SOLICITADA)
            ->orderBy('c.fecha_respuesta', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Carta[] Returns an array of Carta objects
     */
    public function obtenerUltimasCartasProcesadas(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.estado = :estado_aprobado')
            ->orWhere('c.estado = :estado_denegado')
            ->andWhere('c.fecha_respuesta > :haceCincoMinutos')
            ->setParameter('estado_aprobado', Carta::CARTA_APROBADA)
            ->setParameter('estado_denegado', Carta::CARTA_DENEGADA)
            ->setParameter('haceCincoMinutos', DATE_SUB(new DateTime(), DateInterval::createFromDateString('5 minutes')))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $clienteid
     * @return Carta[] Returns an array of Carta objects
     */
    public function obtenerCartasActivasDelCliente($clienteid): array
    {
        return $this->createQueryBuilder('carta')
            ->where('carta.cliente = :cliente_id')
            ->andWhere('carta.estado = :estado_aprobado')
            ->setParameter('cliente_id', $clienteid)
            ->setParameter('estado_aprobado', Carta::CARTA_APROBADA)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $cartaid
     * @param $clienteid
     * @return PersistentCollection Returns an array of Fondo objects
     * @throws NonUniqueResultException
     */
    public function obtenerFondosPorCartaYCliente($cartaid, $clienteid): PersistentCollection
    {
        $carta = $this->createQueryBuilder('carta')
            ->where('carta.cliente = :cliente_id')
            ->andWhere('carta.id = :carta_id')
            ->setParameter('cliente_id', $clienteid)
            ->setParameter('carta_id', $cartaid)
            ->getQuery()
            ->getOneOrNullResult();

        return $carta->getFondos();
    }
}
