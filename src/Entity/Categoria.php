<?php

namespace App\Entity;

use App\Repository\CategoriaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoriaRepository::class)
 * @ORM\Table(name="sac_categoria")
 */
class Categoria
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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $identificador;

    /**
     * @ORM\Column(type="float")
     */
    private $transcripcion_precio;

    /**
     * @ORM\Column(type="float")
     */
    private $reprografia_normal_precio;

    /**
     * @ORM\Column(type="float")
     */
    private $reprografia_grande_precio;

    /**
     * @ORM\Column(type="float")
     */
    private $certificacion_precio;

    /**
     * @ORM\OneToMany(targetEntity=Cliente::class, mappedBy="categoria")
     */
    private $clientes;

    /**
     * Init the array's collections for every new categoria
     */
    public function __construct()
    {
        $this->clientes = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return $this->getNombre();
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
     * @return float|null
     */
    public function getTranscripcionPrecio(): ?float
    {
        return $this->transcripcion_precio;
    }

    /**
     * @param float $transcripcion_precio
     * @return $this
     */
    public function setTranscripcionPrecio(float $transcripcion_precio): self
    {
        $this->transcripcion_precio = $transcripcion_precio;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getReprografiaNormalPrecio(): ?float
    {
        return $this->reprografia_normal_precio;
    }

    /**
     * @param float $reprografia_normal_precio
     * @return $this
     */
    public function setReprografiaNormalPrecio(float $reprografia_normal_precio): self
    {
        $this->reprografia_normal_precio = $reprografia_normal_precio;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getReprografiaGrandePrecio(): ?float
    {
        return $this->reprografia_grande_precio;
    }

    /**
     * @param float $reprografia_grande_precio
     * @return $this
     */
    public function setReprografiaGrandePrecio(float $reprografia_grande_precio): self
    {
        $this->reprografia_grande_precio = $reprografia_grande_precio;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getCertificacionPrecio(): ?float
    {
        return $this->certificacion_precio;
    }

    /**
     * @param float $certificacion_precio
     * @return $this
     */
    public function setCertificacionPrecio(float $certificacion_precio): self
    {
        $this->certificacion_precio = $certificacion_precio;

        return $this;
    }

    /**
     * @return Collection|Cliente[]
     */
    public function getClientes(): Collection
    {
        return $this->clientes;
    }

    /**
     * @param Cliente $cliente
     * @return $this
     */
    public function addCliente(Cliente $cliente): self
    {
        if (!$this->clientes->contains($cliente)) {
            $this->clientes[] = $cliente;
            $cliente->setCategoria($this);
        }

        return $this;
    }

    /**
     * @param Cliente $cliente
     * @return $this
     */
    public function removeCliente(Cliente $cliente): self
    {
        if ($this->clientes->removeElement($cliente)) {
            // set the owning side to null (unless already changed)
            if ($cliente->getCategoria() === $this) {
                $cliente->setCategoria(null);
            }
        }

        return $this;
    }
}
