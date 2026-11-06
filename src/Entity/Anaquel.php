<?php

namespace App\Entity;

use App\Repository\AnaquelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=AnaquelRepository::class)
 * @ORM\Table(name="sac_registro_anaqueles")
 */
class Anaquel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=10, unique=true)
     * @Gedmo\Slug(fields={"numero"}, prefix="anaquel-", unique=false)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Expediente::class, mappedBy="anaquel")
     */
    private $expedientes;

    /**
     * @ORM\OneToMany(targetEntity=Libro::class, mappedBy="anaquel")
     */
    private $libros;

    /**
     * Init the object properties
     */
    public function __construct()
    {
        $this->libros = new ArrayCollection();
        $this->expedientes = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return 'ANA' . $this->getNumero();
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
            $expediente->setAnaquel($this);
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
            if ($expediente->getAnaquel() === $this) {
                $expediente->setAnaquel(null);
            }
        }

        return $this;
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
            $libro->setAnaquel($this);
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
            if ($libro->getAnaquel() === $this) {
                $libro->setAnaquel(null);
            }
        }

        return $this;
    }
}
