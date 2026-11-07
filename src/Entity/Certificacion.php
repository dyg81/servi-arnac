<?php

namespace App\Entity;

use App\Repository\CertificacionRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CertificacionRepository::class)
 * @ORM\Table(name="sac_servicio_certificaciones")
 */
class Certificacion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Cliente::class, inversedBy="certificaciones")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cliente;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_solicitud;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_actualizacion;

    /**
     * @ORM\Column(type="integer")
     */
    private $documento_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $documento_nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $documento_fondo;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $documento_tipo;

    /**
     * @ORM\Column(type="smallint")
     */
    private $estado;

    const ESTADO_CANCELADO = -1;
    const ESTADO_SOLICITADO = 0;
    const ESTADO_EN_PROCESO = 1;
    const ESTADO_TERMINADO = 2;

    /**
     * @return string[]
     */
    static public function getEstados(): array
    {
        $estados = array(
            self::ESTADO_CANCELADO => "Cancelado",
            self::ESTADO_SOLICITADO => "Solicitado",
            self::ESTADO_EN_PROCESO => "Procesando",
            self::ESTADO_TERMINADO => "Terminado",
        );

        return $estados;
    }

    /**
     * @return string
     */
    public function getEstadoCertificacion(): ?string
    {
        $estados = self::getEstados();

        return $estados[$this->estado] ?? sprintf('Estado desconocido (%s)', $this->estado);
    }

    /**
     * Init the object properties
     */
    public function __construct()
    {
        $this->estado = self::ESTADO_SOLICITADO;
        $this->fecha_solicitud = new DateTime();
        $this->fecha_actualizacion = new DateTime();
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
    public function getFechaActualizacion(): ?DateTimeInterface
    {
        return $this->fecha_actualizacion;
    }

    /**
     * @param DateTimeInterface $fecha_actualizacion
     * @return $this
     */
    public function setFechaActualizacion(DateTimeInterface $fecha_actualizacion): self
    {
        $this->fecha_actualizacion = $fecha_actualizacion;

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
    public function getDocumentoNombre(): ?string
    {
        return $this->documento_nombre;
    }

    /**
     * @param string $documento_nombre
     * @return $this
     */
    public function setDocumentoNombre(string $documento_nombre): self
    {
        $this->documento_nombre = $documento_nombre;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDocumentoFondo(): ?string
    {
        return $this->documento_fondo;
    }

    /**
     * @param string $documento_fondo
     * @return $this
     */
    public function setDocumentoFondo(string $documento_fondo): self
    {
        $this->documento_fondo = $documento_fondo;

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
}
