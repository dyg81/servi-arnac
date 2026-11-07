<?php

namespace App\Entity;

use App\Repository\InvestigacionRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvestigacionRepository::class)
 * @ORM\Table(name="sac_sala_investigaciones")
 */
class Investigacion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Cliente::class, inversedBy="investigaciones")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cliente;

    /**
     * @ORM\ManyToOne(targetEntity=Fondo::class, inversedBy="investigaciones")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fondo;

    /**
     * @ORM\ManyToMany(targetEntity=Expediente::class, inversedBy="investigaciones", cascade={"persist"})
     * @ORM\JoinTable(name="sac_sala_investigaciones_expedientes",
     *     joinColumns={@ORM\JoinColumn(name="servicio_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="expediente_id", referencedColumnName="id")}
     * )
     */
    private $expedientes;

    /**
     * @ORM\ManyToMany(targetEntity=Libro::class, inversedBy="investigaciones", cascade={"persist"})
     * @ORM\JoinTable(name="sac_sala_investigaciones_libros",
     *     joinColumns={@ORM\JoinColumn(name="servicio_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="libro_id", referencedColumnName="id")}
     * )
     */
    private $libros;

    /**
     * @ORM\Column(type="smallint")
     */
    private $estado;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_solicitud;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_actualizacion;

    /**
     * @ORM\OneToMany(targetEntity=Vitrina::class, mappedBy="investigacion")
     */
    private $vitrinas;

    const ESTADO_CANCELADO = -1;
    const ESTADO_SOLICITADO = 0;
    const ESTADO_PROCESADO = 1;
    const ESTADO_SALA_RESERVA = 2;
    const ESTADO_SALA_CLIENTE = 3;
    const ESTADO_TERMINADO = 4;
    const ESTADO_PAUSADO = 5;

    /**
     * @return string[]
     */
    static public function getEstados()
    {
        $estados = array(
            self::ESTADO_CANCELADO => "Cancelado",
            self::ESTADO_SOLICITADO => "Solicitado",
            self::ESTADO_PROCESADO => "Procesado",
            self::ESTADO_SALA_RESERVA => "En sala reserva",
            self::ESTADO_SALA_CLIENTE => "En sala cliente",
            self::ESTADO_TERMINADO => "Terminado",
            self::ESTADO_PAUSADO => "En pausa"

        );

        return $estados;
    }

    /**
     * @return string
     */
    public function getEstadoInvestigacion()
    {
        $estados = self::getEstados();

        return $estados[$this->estado] ?? sprintf('Estado desconocido (%s)', $this->estado);
    }

    /**
     * Init the object properties
     */
    public function __construct()
    {
        $this->libros = new ArrayCollection();
        $this->vitrinas = new ArrayCollection();
        $this->expedientes = new ArrayCollection();
        $this->fecha_solicitud = new DateTime();
        $this->fecha_actualizacion = new DateTime();
        $this->estado = self::ESTADO_SOLICITADO;
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
     * @return Collection<int, Expediente>
     */
    public function getExpedientes(): Collection
    {
        return $this->expedientes;
    }

    /**
     * @param Expediente $expediente
     * @return $this
     */
    public function addExpediente(Expediente $expediente): self
    {
        if (!$this->expedientes->contains($expediente)) {
            $this->expedientes[] = $expediente;
        }

        return $this;
    }

    /**
     * @param Expediente $expediente
     * @return $this
     */
    public function removeExpediente(Expediente $expediente): self
    {
        $this->expedientes->removeElement($expediente);

        return $this;
    }

    /**
     * @return mixed
     */
    public function clearExpedientes()
    {
        $this->getExpedientes()->clear();
    }

    /**
     * @return Collection<int, Libro>
     */
    public function getLibros(): Collection
    {
        return $this->libros;
    }

    /**
     * @param Libro $libro
     * @return $this
     */
    public function addLibro(Libro $libro): self
    {
        if (!$this->libros->contains($libro)) {
            $this->libros[] = $libro;
        }

        return $this;
    }

    /**
     * @param Libro $libro
     * @return $this
     */
    public function removeLibro(Libro $libro): self
    {
        $this->libros->removeElement($libro);

        return $this;
    }

    /**
     * @return mixed
     */
    public function clearLibros()
    {
        $this->getLibros()->clear();
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
     * @return Collection<int, Vitrina>
     */
    public function getVitrinas(): Collection
    {
        return $this->vitrinas;
    }

    /**
     * @param Vitrina $vitrina
     * @return $this
     */
    public function addVitrina(Vitrina $vitrina): self
    {
        if (!$this->vitrinas->contains($vitrina)) {
            $this->vitrinas[] = $vitrina;
            $vitrina->setInvestigacion($this);
        }

        return $this;
    }

    /**
     * @param Vitrina $vitrina
     * @return $this
     */
    public function removeVitrina(Vitrina $vitrina): self
    {
        if ($this->vitrinas->removeElement($vitrina)) {
            // set the owning side to null (unless already changed)
            if ($vitrina->getInvestigacion() === $this) {
                $vitrina->setInvestigacion(null);
            }
        }

        return $this;
    }
}
