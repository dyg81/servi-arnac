<?php

namespace App\Entity;

use App\Repository\DepositoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DepositoRepository::class)
 * @ORM\Table(name="sac_deposito")
 */
class Deposito
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
     * @ORM\Column(type="string", length=7)
     */
    private $identificador;

    /**
     * @ORM\ManyToMany(targetEntity=Fondo::class, mappedBy="depositos")
     */
    private $fondos;

    /**
     * @ORM\OneToMany(targetEntity=Expediente::class, mappedBy="deposito")
     */
    private $expedientes;

    /**
     * @ORM\OneToMany(targetEntity=Libro::class, mappedBy="deposito")
     */
    private $libros;

    /**
     * Init the array's collections for every new deposito
     */
    public function __construct()
    {
        $this->fondos = new ArrayCollection();
        $this->expedientes = new ArrayCollection();
        $this->libros = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return $this->getIdentificador();
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
            $fondo->addDeposito($this);
        }

        return $this;
    }

    /**
     * @param Fondo $fondo
     * @return $this
     */
    public function removeFondo(Fondo $fondo): self
    {
        if ($this->fondos->removeElement($fondo)) {
            $fondo->removeDeposito($this);
        }

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
            $expediente->setDeposito($this);
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
            if ($expediente->getDeposito() === $this) {
                $expediente->setDeposito(null);
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
            $libro->setDeposito($this);
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
            if ($libro->getDeposito() === $this) {
                $libro->setDeposito(null);
            }
        }

        return $this;
    }
}
