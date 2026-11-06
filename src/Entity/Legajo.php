<?php

namespace App\Entity;

use App\Repository\LegajoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=LegajoRepository::class)
 * @ORM\Table(name="sac_registro_legajos")
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
     * @ORM\Column(type="string", length=3)
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=10, unique=true)
     * @Gedmo\Slug(fields={"numero"}, prefix="legajo-", unique=false)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Expediente::class, mappedBy="legajo")
     */
    private $expedientes;

    /**
     * Init the object properties
     */
    public function __construct()
    {
        $this->expedientes = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return 'LEG' . $this->getNumero();
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
