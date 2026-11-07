<?php

namespace App\Entity;

use App\Repository\LibroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=LibroRepository::class)
 * @ORM\Table(name="sac_registro_libros")
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
     * @ORM\Column(type="string", length=3)
     */
    private $tomo;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $anno;

    /**
     * @ORM\Column(type="text")
     */
    private $contenido;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\RelativeSlugHandler", options={
     *          @Gedmo\SlugHandlerOption(name="relationField", value="fondo"),
     *          @Gedmo\SlugHandlerOption(name="relationSlugField", value="slug"),
     *          @Gedmo\SlugHandlerOption(name="separator", value="-libro-")
     *      })
     * }, fields={"anno", "tomo"}, unique=false)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity=Fondo::class, inversedBy="libros")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fondo;

    /**
     * @ORM\ManyToOne(targetEntity=Deposito::class, inversedBy="libros")
     * @ORM\JoinColumn(nullable=false)
     */
    private $deposito;

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

    /**
     * @ORM\ManyToMany(targetEntity=Investigacion::class, mappedBy="libros")
     */
    private $investigaciones;

    /**
     * @ORM\Column(type="boolean")
     */
    private $vitrina;

    const LIB_ESTADO_ARCHIVADO_EN_DEPOSITO = 0;
    const LIB_ESTADO_SOLICITADO_PARA_SALA = 1;
    const LIB_ESTADO_RESERVADO_EN_SALA = 2;
    const LIB_ESTADO_EN_SALA_RECEPCION = 3;
    const LIB_ESTADO_RESERVADO_PARA_TRANS = 4;
    const LIB_ESTADO_RESERVADO_PARA_DIG = 5;
    const LIB_ESTADO_RESERVADO_PARA_CERT = 6;
    const LIB_ESTADO_TRANS_EN_PROCESO = 7;
    const LIB_ESTADO_DIG_EN_PROCESO = 8;
    const LIB_ESTADO_CERT_EN_PROCESO = 9;
    const LIB_ESTADO_TRANS_TERMINADA = 10;
    const LIB_ESTADO_DIG_TERMINADA = 11;
    const LIB_ESTADO_CERT_TERMINADA = 12;
    const LIB_ESTADO_EN_VITRINA = 13;
    const LIB_ESTADO_NO_ENCONTRADO = 98;
    const LIB_ESTADO_NO_USABLE = 99;

    /**
     * @return array
     */
    static public function getEstados(): array
    {
        $estados = array(
            self::LIB_ESTADO_ARCHIVADO_EN_DEPOSITO => "Archivado en Depósito",
            self::LIB_ESTADO_SOLICITADO_PARA_SALA => "Solicitado para Sala",
            self::LIB_ESTADO_RESERVADO_EN_SALA => "Reservado en Sala",
            self::LIB_ESTADO_EN_SALA_RECEPCION => "En Recepción",
            self::LIB_ESTADO_RESERVADO_PARA_TRANS => "Reservado para Transcripción",
            self::LIB_ESTADO_RESERVADO_PARA_DIG => "Reservado para Digitalización",
            self::LIB_ESTADO_RESERVADO_PARA_CERT => "Reservado para Certificación",
            self::LIB_ESTADO_TRANS_EN_PROCESO => "Transcripción en Proceso",
            self::LIB_ESTADO_DIG_EN_PROCESO => "Digitalización en Proceso",
            self::LIB_ESTADO_CERT_EN_PROCESO => "Certificación en Proceso",
            self::LIB_ESTADO_TRANS_TERMINADA => "Transcripción Terminada",
            self::LIB_ESTADO_DIG_TERMINADA => "Reprografía Terminada",
            self::LIB_ESTADO_CERT_TERMINADA => "Certificación Terminada",
            self::LIB_ESTADO_EN_VITRINA => "Libro en Vitrina",
            self::LIB_ESTADO_NO_ENCONTRADO => "Libro no Encontrado",
            self::LIB_ESTADO_NO_USABLE => "Libro no Usable"
        );

        return $estados;
    }

    /**
     * @return mixed
     */
    public function getEstadoLibro()
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
        $this->estado = self::LIB_ESTADO_ARCHIVADO_EN_DEPOSITO;
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return 'Año: ' . $this->getAnno() . ' Tomo: ' . $this->getTomo();
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
            $investigacione->addLibro($this);
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
            $investigacione->removeLibro($this);
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
