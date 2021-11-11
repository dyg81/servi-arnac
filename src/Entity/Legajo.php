<?php

namespace App\Entity;

use App\Repository\LegajoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LegajoRepository::class)
 * @ORM\Table(name="sac_legajo")
 */
class Legajo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3, unique=true)
     */
    private $legajo;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private $identificador;

    /**
     * @ORM\OneToMany(targetEntity=Expediente::class, mappedBy="legajo")
     */
    private $expedientes;

    /**
     * Init the array's collections for every new legajo
     */
    public function __construct()
    {
        $this->expedientes = new ArrayCollection();
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
    public function getLegajo(): ?string
    {
        return $this->legajo;
    }

    /**
     * @param string $legajo
     * @return $this
     */
    public function setLegajo(string $legajo): self
    {
        $this->legajo = $legajo;

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
            $expediente->setLegajo($this);
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
            if ($expediente->getLegajo() === $this) {
                $expediente->setLegajo(null);
            }
        }

        return $this;
    }
}
