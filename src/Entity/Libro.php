<?php

namespace App\Entity;

use App\Repository\LibroRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LibroRepository::class)
 * @ORM\Table(name="sac_libro")
 */
class Libro
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $tomo;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $anno;

    /**
     * @ORM\Column(type="text")
     */
    private $descripcion;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $identificador;

    /**
     * @ORM\ManyToOne(targetEntity=Deposito::class, inversedBy="libros")
     * @ORM\JoinColumn(nullable=false)
     */
    private $deposito;

    /**
     * @ORM\ManyToOne(targetEntity=Fondo::class, inversedBy="libros")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fondo;

    /**
     * @ORM\ManyToOne(targetEntity=Estante::class, inversedBy="libros")
     * @ORM\JoinColumn(nullable=false)
     */
    private $estante;

    /**
     * @ORM\ManyToOne(targetEntity=Anaquel::class, inversedBy="libros")
     * @ORM\JoinColumn(nullable=false)
     */
    private $anaquel;

    /**
     * @ORM\Column(type="smallint")
     */
    private $estado;

    const LIB_ESTADO_ARCHIVADO_EN_DEPOSITO  = 0;
    const LIB_ESTADO_SOLICITADO_PARA_SALA   = 1;
    const LIB_ESTADO_RESERVADO_EN_SALA      = 2;
    const LIB_ESTADO_EN_SALA_RECEPCION      = 3;
    const LIB_ESTADO_RESERVADO_PARA_TRANS   = 4;
    const LIB_ESTADO_RESERVADO_PARA_DIG     = 5;
    const LIB_ESTADO_RESERVADO_PARA_CERT    = 6;
    const LIB_ESTADO_TRANS_EN_PROCESO       = 7;
    const LIB_ESTADO_DIG_EN_PROCESO         = 8;
    const LIB_ESTADO_CERT_EN_PROCESO        = 9;
    const LIB_ESTADO_TRANS_TERMINADA        = 10;
    const LIB_ESTADO_DIG_TERMINADA          = 11;
    const LIB_ESTADO_CERT_TERMINADA         = 12;
    const LIB_ESTADO_EN_VITRINA             = 13;
    const LIB_ESTADO_NO_ENCONTRADO          = 98;
    const LIB_ESTADO_NO_USABLE              = 99;

    /**
     * @return array
     */
    static public function getEstados(): array
    {
        $estados = array(
            self::LIB_ESTADO_ARCHIVADO_EN_DEPOSITO  => "Archivado en Depósito",
            self::LIB_ESTADO_SOLICITADO_PARA_SALA   => "Solicitado para Sala",
            self::LIB_ESTADO_RESERVADO_EN_SALA      => "Reservado en Sala",
            self::LIB_ESTADO_EN_SALA_RECEPCION      => "En Recepción",
            self::LIB_ESTADO_RESERVADO_PARA_TRANS   => "Reservado para Transcripción",
            self::LIB_ESTADO_RESERVADO_PARA_DIG     => "Reservado para Digitalización",
            self::LIB_ESTADO_RESERVADO_PARA_CERT    => "Reservado para Certificación",
            self::LIB_ESTADO_TRANS_EN_PROCESO       => "Transcripción en Proceso",
            self::LIB_ESTADO_DIG_EN_PROCESO         => "Digitalización en Proceso",
            self::LIB_ESTADO_CERT_EN_PROCESO        => "Certificación en Proceso",
            self::LIB_ESTADO_TRANS_TERMINADA        => "Transcripción Terminada",
            self::LIB_ESTADO_DIG_TERMINADA          => "Transcripción Terminada",
            self::LIB_ESTADO_CERT_TERMINADA         => "Certificación Terminada",
            self::LIB_ESTADO_EN_VITRINA             => "Libro en Vitrina",
            self::LIB_ESTADO_NO_ENCONTRADO          => "Libro no Encontrado",
            self::LIB_ESTADO_NO_USABLE              => "Libro no Usable"
        );

        return $estados;
    }

    /**
     * @return mixed
     */
    public function getEstadoLibro()
    {
        $estados = self::getEstados();

        return $estados[$this->estado];
    }

    /**
     * Init the state of every new libro
     */
    public function __construct()
    {
        $this->estado = self::LIB_ESTADO_ARCHIVADO_EN_DEPOSITO;
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
    public function getTomo(): ?string
    {
        return $this->tomo;
    }

    /**
     * @param string $tomo
     * @return $this
     */
    public function setTomo(string $tomo): self
    {
        $this->tomo = $tomo;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAnno(): ?string
    {
        return $this->anno;
    }

    /**
     * @param string $anno
     * @return $this
     */
    public function setAnno(string $anno): self
    {
        $this->anno = $anno;

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
     * @return Deposito|null
     */
    public function getDeposito(): ?Deposito
    {
        return $this->deposito;
    }

    /**
     * @param Deposito|null $deposito
     * @return $this
     */
    public function setDeposito(?Deposito $deposito): self
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
     * @return Estante|null
     */
    public function getEstante(): ?Estante
    {
        return $this->estante;
    }

    /**
     * @param Estante|null $estante
     * @return $this
     */
    public function setEstante(?Estante $estante): self
    {
        $this->estante = $estante;

        return $this;
    }

    /**
     * @return Anaquel|null
     */
    public function getAnaquel(): ?Anaquel
    {
        return $this->anaquel;
    }

    /**
     * @param Anaquel|null $anaquel
     * @return $this
     */
    public function setAnaquel(?Anaquel $anaquel): self
    {
        $this->anaquel = $anaquel;

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
