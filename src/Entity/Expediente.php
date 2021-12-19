<?php

namespace App\Entity;

use App\Repository\ExpedienteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExpedienteRepository::class)
 * @ORM\Table(name="sac_expediente")
 */
class Expediente
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $identificador;

    /**
     * @ORM\ManyToOne(targetEntity=Deposito::class, inversedBy="expedientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $deposito;

    /**
     * @ORM\ManyToOne(targetEntity=Fondo::class, inversedBy="expedientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fondo;

    /**
     * @ORM\ManyToOne(targetEntity=Legajo::class, inversedBy="expedientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $legajo;

    /**
     * @ORM\ManyToOne(targetEntity=Estante::class, inversedBy="expedientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $estante;

    /**
     * @ORM\ManyToOne(targetEntity=Anaquel::class, inversedBy="expedientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $anaquel;

    /**
     * @ORM\Column(type="text")
     */
    private $descripcion;

    /**
     * @ORM\Column(type="smallint")
     */
    private $estado;

    const EXP_ESTADO_ARCHIVADO_EN_DEPOSITO  = 0;
    const EXP_ESTADO_SOLICITADO_PARA_SALA   = 1;
    const EXP_ESTADO_RESERVADO_EN_SALA      = 2;
    const EXP_ESTADO_EN_SALA_RECEPCION      = 3;
    const EXP_ESTADO_RESERVADO_PARA_TRANS   = 4;
    const EXP_ESTADO_RESERVADO_PARA_DIG     = 5;
    const EXP_ESTADO_RESERVADO_PARA_CERT    = 6;
    const EXP_ESTADO_TRANS_EN_PROCESO       = 7;
    const EXP_ESTADO_DIG_EN_PROCESO         = 8;
    const EXP_ESTADO_CERT_EN_PROCESO        = 9;
    const EXP_ESTADO_TRANS_TERMINADA        = 10;
    const EXP_ESTADO_DIG_TERMINADA          = 11;
    const EXP_ESTADO_CERT_TERMINADA         = 12;
    const EXP_ESTADO_EN_VITRINA             = 13;
    const EXP_ESTADO_NO_ENCONTRADO          = 98;
    const EXP_ESTADO_NO_USABLE              = 99;

    /**
     * @return array
     */
    static public function getEstados(): array
    {
        $estados = array(
            self::EXP_ESTADO_ARCHIVADO_EN_DEPOSITO  => "Archivado en Depósito",
            self::EXP_ESTADO_SOLICITADO_PARA_SALA   => "Solicitado para Sala",
            self::EXP_ESTADO_RESERVADO_EN_SALA      => "Reservado en Sala",
            self::EXP_ESTADO_EN_SALA_RECEPCION      => "En Recepción",
            self::EXP_ESTADO_RESERVADO_PARA_TRANS   => "Reservado para Transcripción",
            self::EXP_ESTADO_RESERVADO_PARA_DIG     => "Reservado para Digitalización",
            self::EXP_ESTADO_RESERVADO_PARA_CERT    => "Reservado para Certificación",
            self::EXP_ESTADO_TRANS_EN_PROCESO       => "Transcripción en Proceso",
            self::EXP_ESTADO_DIG_EN_PROCESO         => "Digitalización en Proceso",
            self::EXP_ESTADO_CERT_EN_PROCESO        => "Certificación en Proceso",
            self::EXP_ESTADO_TRANS_TERMINADA        => "Transcripción Terminada",
            self::EXP_ESTADO_DIG_TERMINADA          => "Transcripción Terminada",
            self::EXP_ESTADO_CERT_TERMINADA         => "Certificación Terminada",
            self::EXP_ESTADO_EN_VITRINA             => "Expediente en Vitrina",
            self::EXP_ESTADO_NO_ENCONTRADO          => "Expediente no Encontrado",
            self::EXP_ESTADO_NO_USABLE              => "Expediente no Usable"
        );

        return $estados;
    }

    /**
     * @return mixed
     */
    public function getEstadoExpediente()
    {
        $estados = self::getEstados();

        return $estados[$this->estado];
    }

    /**
     * Init the state of every new expediente
     */
    public function __construct()
    {
        $this->estado = self::EXP_ESTADO_ARCHIVADO_EN_DEPOSITO;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getNumero(): ?string
    {
        return $this->numero;
    }

    /**
     * @param string $numero
     * @return $this
     */
    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIdentificador(): ?string
    {
        return $this->identificador;
    }

    /**
     * @param string $identificador
     * @return $this
     */
    public function setIdentificador(string $identificador): self
    {
        $this->identificador = $identificador;

        return $this;
    }

    /**
     * @return deposito|null
     */
    public function getDeposito(): ?deposito
    {
        return $this->deposito;
    }

    /**
     * @param deposito|null $deposito
     * @return $this
     */
    public function setDeposito(?deposito $deposito): self
    {
        $this->deposito = $deposito;

        return $this;
    }

    /**
     * @return Fondo|null
     */
    public function getFondo(): ?Fondo
    {
        return $this->fondo;
    }

    /**
     * @param Fondo|null $fondo
     * @return $this
     */
    public function setFondo(?Fondo $fondo): self
    {
        $this->fondo = $fondo;

        return $this;
    }

    /**
     * @return legajo|null
     */
    public function getLegajo(): ?legajo
    {
        return $this->legajo;
    }

    /**
     * @param legajo|null $legajo
     * @return $this
     */
    public function setLegajo(?legajo $legajo): self
    {
        $this->legajo = $legajo;

        return $this;
    }

    /**
     * @return estante|null
     */
    public function getEstante(): ?estante
    {
        return $this->estante;
    }

    /**
     * @param estante|null $estante
     * @return $this
     */
    public function setEstante(?estante $estante): self
    {
        $this->estante = $estante;

        return $this;
    }

    /**
     * @return anaquel|null
     */
    public function getAnaquel(): ?anaquel
    {
        return $this->anaquel;
    }

    /**
     * @param anaquel|null $anaquel
     * @return $this
     */
    public function setAnaquel(?anaquel $anaquel): self
    {
        $this->anaquel = $anaquel;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    /**
     * @param string $descripcion
     * @return $this
     */
    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

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
