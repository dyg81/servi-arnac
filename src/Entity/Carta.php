<?php

namespace App\Entity;

use App\Repository\CartaRepository;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CartaRepository::class)
 * @ORM\Table(name="sac_sala_cartas")
 */
class Carta
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Cliente::class, inversedBy="cartas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cliente;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $documento;

    /**
     * @ORM\Column(type="smallint")
     */
    private $estado;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observaciones;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_solicitud;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_respuesta;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_vencimiento;

    /**
     * @ORM\ManyToMany(targetEntity=Fondo::class, inversedBy="cartas")
     * @ORM\JoinTable(name="sac_sala_cartas_fondos")
     */
    private $fondos;

    const CARTA_SOLICITADA = 0;
    const CARTA_APROBADA = 1;
    const CARTA_DENEGADA = 2;
    const CARTA_VENCIDA = 3;


    /**
     * Init the object properties
     */
    public function __construct()
    {
        $this->estado = self::CARTA_SOLICITADA;
        $this->fondos = new ArrayCollection();
        $this->fecha_solicitud = new DateTime();
        $this->fecha_respuesta = DATE_ADD(new DateTime(), DateInterval::createFromDateString('3 day'));
        $this->fecha_vencimiento = DATE_ADD(new DateTime(), DateInterval::createFromDateString('1 year'));
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Cliente|null
     */
    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }

    /**
     * @param Cliente|null $cliente
     * @return $this
     */
    public function setCliente(?Cliente $cliente): self
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDocumento(): ?string
    {
        return $this->documento;
    }

    /**
     * @param string $documento
     * @return $this
     */
    public function setDocumento(string $documento): self
    {
        $this->documento = $documento;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getEstado(): ?int
    {
        return $this->estado;
    }

    /**
     * @param int $estado
     * @return $this
     */
    public function setEstado(int $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    /**
     * @param string $observaciones
     * @return $this
     */
    public function setObservaciones(string $observaciones): self
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getFechaSolicitud(): ?DateTimeInterface
    {
        return $this->fecha_solicitud;
    }

    /**
     * @param DateTimeInterface $fecha_solicitud
     * @return $this
     */
    public function setFechaSolicitud(DateTimeInterface $fecha_solicitud): self
    {
        $this->fecha_solicitud = $fecha_solicitud;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getFechaRespuesta(): ?DateTimeInterface
    {
        return $this->fecha_respuesta;
    }

    /**
     * @param DateTimeInterface $fecha_respuesta
     * @return $this
     */
    public function setFechaRespuesta(DateTimeInterface $fecha_respuesta): self
    {
        $this->fecha_respuesta = $fecha_respuesta;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getFechaVencimiento(): ?DateTimeInterface
    {
        return $this->fecha_vencimiento;
    }

    /**
     * @param DateTimeInterface $fecha_vencimiento
     * @return $this
     */
    public function setFechaVencimiento(DateTimeInterface $fecha_vencimiento): self
    {
        $this->fecha_vencimiento = $fecha_vencimiento;

        return $this;
    }

    /**
     * @return Collection<int, Fondo>
     */
    public function getFondos(): Collection
    {
        return $this->fondos;
    }

    /**
     * @param Fondo $fondo
     * @return $this
     */
    public function addFondo(Fondo $fondo): self
    {
        if (!$this->fondos->contains($fondo)) {
            $this->fondos[] = $fondo;
        }

        return $this;
    }

    /**
     * @param Fondo $fondo
     * @return $this
     */
    public function removeFondo(Fondo $fondo): self
    {
        $this->fondos->removeElement($fondo);

        return $this;
    }
}
