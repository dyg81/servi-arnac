<?php

namespace App\Entity;

use App\Repository\EstanteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EstanteRepository::class)
 * @ORM\Table(name="sac_estante")
 */
class Estante
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2, unique=true)
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $identificador;

    /**
     * @ORM\OneToMany(targetEntity=Expediente::class, mappedBy="estante")
     */
    private $expedientes;

    /**
     * @ORM\OneToMany(targetEntity=Libro::class, mappedBy="estante")
     */
    private $libros;

    /**
     * Init the array's collections for every new estante
     */
    public function __construct()
    {
        $this->expedientes = new ArrayCollection();
        $this->libros = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->identificador;
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
            $expediente->setEstante($this);
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
            if ($expediente->getEstante() === $this) {
                $expediente->setEstante(null);
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

    public function addLibro(Libro $libro): self
    {
        if (!$this->libros->contains($libro)) {
            $this->libros[] = $libro;
            $libro->setEstante($this);
        }

        return $this;
    }

    public function removeLibro(Libro $libro): self
    {
        if ($this->libros->removeElement($libro)) {
            // set the owning side to null (unless already changed)
            if ($libro->getEstante() === $this) {
                $libro->setEstante(null);
            }
        }

        return $this;
    }
}
