<?php

namespace App\Entity;

use App\Repository\VitrinaRepository;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VitrinaRepository::class)
 * @ORM\Table(name="sac_sala_vitrinas")
 */
class Vitrina
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Investigacion::class, inversedBy="vitrinas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $investigacion;

    /**
     * @ORM\Column(type="integer")
     */
    private $documento_id;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $documento_tipo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_vencimiento;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $documento_nombre;

    /**
     * Init the object properties
     */
    public function __construct()
    {
        $this->fecha_vencimiento = DATE_ADD(new DateTime(), DateInterval::createFromDateString('15 days'));
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Investigacion|null
     */
    public function getInvestigacion(): ?Investigacion
    {
        return $this->investigacion;
    }

    /**
     * @param Investigacion|null $investigacion
     * @return $this
     */
    public function setInvestigacion(?Investigacion $investigacion): self
    {
        $this->investigacion = $investigacion;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDocumentoId(): ?int
    {
        return $this->documento_id;
    }

    /**
     * @param int $documento_id
     * @return $this
     */
    public function setDocumentoId(int $documento_id): self
    {
        $this->documento_id = $documento_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDocumentoTipo(): ?string
    {
        return $this->documento_tipo;
    }

    /**
     * @param string $documento_tipo
     * @return $this
     */
    public function setDocumentoTipo(string $documento_tipo): self
    {
        $this->documento_tipo = $documento_tipo;

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
     * @return string|null
     */
    public function getDocumentoNombre(): ?string
    {
        return $this->documento_nombre;
    }

    /**
     * @param string|null $documento_nombre
     * @return $this
     */
    public function setDocumentoNombre(?string $documento_nombre): self
    {
        $this->documento_nombre = $documento_nombre;

        return $this;
    }
}
