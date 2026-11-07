<?php

namespace App\Entity;

use App\Repository\ExpedienteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ExpedienteRepository::class)
 * @ORM\Table(name="sac_registro_expedientes")
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
     * @ORM\Column(type="string", length=4)
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\RelativeSlugHandler", options={
     *          @Gedmo\SlugHandlerOption(name="relationField", value="fondo"),
     *          @Gedmo\SlugHandlerOption(name="relationSlugField", value="slug"),
     *          @Gedmo\SlugHandlerOption(name="separator", value="-expediente-")
     *      })
     * }, fields={"numero"}, unique=false)
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     */
    private $contenido;

    /**
     * @ORM\ManyToOne(targetEntity=Fondo::class, inversedBy="expedientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fondo;

    /**
     * @ORM\ManyToOne(targetEntity=Deposito::class, inversedBy="expedientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $deposito;

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
     * @ORM\ManyToOne(targetEntity=Legajo::class, inversedBy="expedientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $legajo;

    /**
     * @ORM\Column(type="smallint")
     */
    private $estado;

    /**
     * @ORM\ManyToMany(targetEntity=Investigacion::class, mappedBy="expedientes")
     */
    private $investigaciones;

    /**
     * @ORM\Column(type="boolean")
     */
    private $vitrina;

    const EXP_ESTADO_ARCHIVADO_EN_DEPOSITO = 0;
    const EXP_ESTADO_SOLICITADO_PARA_SALA = 1;
    const EXP_ESTADO_RESERVADO_EN_SALA = 2;
    const EXP_ESTADO_EN_SALA_RECEPCION = 3;
    const EXP_ESTADO_RESERVADO_PARA_TRANS = 4;
    const EXP_ESTADO_RESERVADO_PARA_DIG = 5;
    const EXP_ESTADO_RESERVADO_PARA_CERT = 6;
    const EXP_ESTADO_TRANS_EN_PROCESO = 7;
    const EXP_ESTADO_DIG_EN_PROCESO = 8;
    const EXP_ESTADO_CERT_EN_PROCESO = 9;
    const EXP_ESTADO_TRANS_TERMINADA = 10;
    const EXP_ESTADO_DIG_TERMINADA = 11;
    const EXP_ESTADO_CERT_TERMINADA = 12;
    const EXP_ESTADO_EN_VITRINA = 13;
    const EXP_ESTADO_NO_ENCONTRADO = 98;
    const EXP_ESTADO_NO_USABLE = 99;


    /**
     * @return array
     */
    static public function getEstados(): array
    {
        $estados = array(
            self::EXP_ESTADO_ARCHIVADO_EN_DEPOSITO => "Archivado en Depósito",
            self::EXP_ESTADO_SOLICITADO_PARA_SALA => "Solicitado para Sala",
            self::EXP_ESTADO_RESERVADO_EN_SALA => "Reservado en Sala",
            self::EXP_ESTADO_EN_SALA_RECEPCION => "En Recepción",
            self::EXP_ESTADO_RESERVADO_PARA_TRANS => "Reservado para Transcripción",
            self::EXP_ESTADO_RESERVADO_PARA_DIG => "Reservado para Digitalización",
            self::EXP_ESTADO_RESERVADO_PARA_CERT => "Reservado para Certificación",
            self::EXP_ESTADO_TRANS_EN_PROCESO => "Transcripción en Proceso",
            self::EXP_ESTADO_DIG_EN_PROCESO => "Digitalización en Proceso",
            self::EXP_ESTADO_CERT_EN_PROCESO => "Certificación en Proceso",
            self::EXP_ESTADO_TRANS_TERMINADA => "Transcripción Terminada",
            self::EXP_ESTADO_DIG_TERMINADA => "Reprografía Terminada",
            self::EXP_ESTADO_CERT_TERMINADA => "Certificación Terminada",
            self::EXP_ESTADO_EN_VITRINA => "Expediente en Vitrina",
            self::EXP_ESTADO_NO_ENCONTRADO => "Expediente no Encontrado",
            self::EXP_ESTADO_NO_USABLE => "Expediente no Usable"
        );

        return $estados;
    }

    /**
     * @return string
     */
    public function getEstadoExpediente(): string
    {
        $estados = self::getEstados();

        return $estados[$this->estado] ?? sprintf('Estado desconocido (%s)', $this->estado);
    }

    /**
     * Init the object properties
     */
    public function __construct()
    {
        $this->vitrina = false;
        $this->investigaciones = new ArrayCollection();
        $this->estado = self::EXP_ESTADO_ARCHIVADO_EN_DEPOSITO;
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return 'EXP' . $this->getNumero();
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
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return $this
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContenido(): ?string
    {
        return $this->contenido;
    }

    /**
     * @param string $contenido
     * @return $this
     */
    public function setContenido(string $contenido): self
    {
        $this->contenido = $contenido;

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
     * @return Legajo|null
     */
    public function getLegajo(): ?Legajo
    {
        return $this->legajo;
    }

    /**
     * @param Legajo|null $legajo
     * @return $this
     */
    public function setLegajo(?Legajo $legajo): self
    {
        $this->legajo = $legajo;

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
     * @return Collection<int, Investigacion>
     */
    public function getInvestigaciones(): Collection
    {
        return $this->investigaciones;
    }

    /**
     * @param Investigacion $investigacione
     * @return $this
     */
    public function addInvestigacione(Investigacion $investigacione): self
    {
        if (!$this->investigaciones->contains($investigacione)) {
            $this->investigaciones[] = $investigacione;
            $investigacione->addExpediente($this);
        }

        return $this;
    }

    /**
     * @param Investigacion $investigacione
     * @return $this
     */
    public function removeInvestigacione(Investigacion $investigacione): self
    {
        if ($this->investigaciones->removeElement($investigacione)) {
            $investigacione->removeExpediente($this);
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getVitrina(): ?bool
    {
        return $this->vitrina;
    }

    /**
     * @param bool $vitrina
     * @return $this
     */
    public function setVitrina(bool $vitrina): self
    {
        $this->vitrina = $vitrina;

        return $this;
    }
}
