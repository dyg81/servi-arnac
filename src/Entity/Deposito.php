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
     * @ORM\Column(type="string", length=8)
     */
    private $identificador;

    /**
     * @ORM\ManyToMany(targetEntity=Fondo::class, mappedBy="depositos")
     */
    private $fondos;

    public function __construct()
    {
        $this->fondos = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getIdentificador();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

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

    /**
     * @return Collection|Fondo[]
     */
    public function getFondos(): Collection
    {
        return $this->fondos;
    }

    public function addFondo(Fondo $fondo): self
    {
        if (!$this->fondos->contains($fondo)) {
            $this->fondos[] = $fondo;
            $fondo->addDeposito($this);
        }

        return $this;
    }

    public function removeFondo(Fondo $fondo): self
    {
        if ($this->fondos->removeElement($fondo)) {
            $fondo->removeDeposito($this);
        }

        return $this;
    }
}
