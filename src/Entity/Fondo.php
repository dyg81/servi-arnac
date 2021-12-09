<?php

namespace App\Entity;

use App\Repository\FondoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FondoRepository::class)
 * @ORM\Table(name="sac_fondo")
 */
class Fondo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="text")
     */
    private $descripcion;

    /**
     * @ORM\ManyToMany(targetEntity=Deposito::class, inversedBy="fondos")
     */
    private $depositos;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $identificador;

    /**
     * @ORM\OneToMany(targetEntity=Expediente::class, mappedBy="fondo")
     */
    private $expedientes;

    /**
     * @ORM\OneToMany(targetEntity=Libro::class, mappedBy="fondo")
     */
    private $libros;

    /**
     * Init the array's collections for every new fondo
     */
    public function __construct()
    {
        $this->depositos = new ArrayCollection();
        $this->expedientes = new ArrayCollection();
        $this->libros = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->nombre;
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
    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    /**
     * @param string $nombre
     * @return $this
     */
    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

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
     * @return Collection|Deposito[]
     */
    public function getDepositos(): Collection
    {
        return $this->depositos;
    }

    /**
     * @param Deposito $deposito
     * @return $this
     */
    public function addDeposito(Deposito $deposito): self
    {
        if (!$this->depositos->contains($deposito)) {
            $this->depositos[] = $deposito;
        }

        return $this;
    }

    /**
     * @param Deposito $deposito
     * @return $this
     */
    public function removeDeposito(Deposito $deposito): self
    {
        $this->depositos->removeElement($deposito);

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
     * @return Collection|Expediente[]
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
            $expediente->setFondo($this);
        }

        return $this;
    }

    /**
     * @param Expediente $expediente
     * @return $this
     */
    public function removeExpediente(Expediente $expediente): self
    {
        if ($this->expedientes->removeElement($expediente)) {
            // set the owning side to null (unless already changed)
            if ($expediente->getFondo() === $this) {
                $expediente->setFondo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Libro[]
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
            $libro->setFondo($this);
        }

        return $this;
    }

    /**
     * @param Libro $libro
     * @return $this
     */
    public function removeLibro(Libro $libro): self
    {
        if ($this->libros->removeElement($libro)) {
            // set the owning side to null (unless already changed)
            if ($libro->getFondo() === $this) {
                $libro->setFondo(null);
            }
        }

        return $this;
    }
}
