<?php

namespace App\Entity;

use App\Repository\CartaRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CartaRepository::class)
 * @ORM\Table(name="sac_carta")
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
     * @ORM\Column(type="date")
     */
    private $fecha_solicitud;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fecha_respuesta;

    /**
     * @ORM\ManyToMany(targetEntity=Fondo::class, inversedBy="cartas")
     */
    private $fondos;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observaciones;

    /**
     * Init the state of every new carta
     */
    public function __construct()
    {
        $this->estado = 0;
        $this->fecha_solicitud = new \DateTime('now');
        $this->fondos = new ArrayCollection();
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
     * @param DateTimeInterface|null $fecha_respuesta
     * @return $this
     */
    public function setFechaRespuesta(?DateTimeInterface $fecha_respuesta): self
    {
        $this->fecha_respuesta = $fecha_respuesta;

        return $this;
    }

    /**
     * @return Collection|Fondo[]
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

    /**
     * @return string|null
     */
    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    /**
     * @param string|null $observaciones
     * @return $this
     */
    public function setObservaciones(?string $observaciones): self
    {
        $this->observaciones = $observaciones;

        return $this;
    }
}
