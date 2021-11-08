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

    public function __construct()
    {
        $this->depositos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

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

    public function addDeposito(Deposito $deposito): self
    {
        if (!$this->depositos->contains($deposito)) {
            $this->depositos[] = $deposito;
        }

        return $this;
    }

    public function removeDeposito(Deposito $deposito): self
    {
        $this->depositos->removeElement($deposito);

        return $this;
    }

    public function getIdentificador(): ?string
    {
        return $this->identificador;
    }

    public function setIdentificador(string $identificador): self
    {
        $this->identificador = $identificador;

        return $this;
    }
}