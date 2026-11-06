<?php

namespace App\Entity;

use App\Repository\FondoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=FondoRepository::class)
 * @ORM\Table(name="sac_registro_fondos")
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
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"nombre"}, prefix="fondo-", unique=false)
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     */
    private $contenido;

    /**
     * @ORM\ManyToMany(targetEntity=Deposito::class, inversedBy="fondos")
     * @ORM\JoinTable(name="sac_registro_fondos_depositos")
     */
    private $depositos;

    /**
     * @ORM\OneToMany(targetEntity=Expediente::class, mappedBy="fondo")
     */
    private $expedientes;

    /**
     * @ORM\OneToMany(targetEntity=Libro::class, mappedBy="fondo")
     */
    private $libros;

    /**
     * @ORM\ManyToMany(targetEntity=Carta::class, mappedBy="fondos")
     */
    private $cartas;

    /**
     * @ORM\OneToMany(targetEntity=Investigacion::class, mappedBy="fondo")
     */
    private $investigaciones;

    /**
     * @ORM\Column(type="integer")
     */
    private $contador;

    /**
     * Init the object properties
     */
    public function __construct()
    {
        $this->setContador(0);
        $this->libros = new ArrayCollection();
        $this->cartas = new ArrayCollection();
        $this->depositos = new ArrayCollection();
        $this->expedientes = new ArrayCollection();
        $this->investigaciones = new ArrayCollection();
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
     * @return string|null
     */
    public function getContenido(): ?string
    {
        return $this->contenido;
    }

    /**
     * @param string $contenido
     * @return $this
     */
    public function setContenido(string $contenido): self
    {
        $this->contenido = $contenido;

        return $this;
    }

    /**
     * @return Collection<int, Deposito>
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

    /**
     * @return Collection<int, Carta>
     */
    public function getCartas(): Collection
    {
        return $this->cartas;
    }

    /**
     * @param Carta $carta
     * @return $this
     */
    public function addCarta(Carta $carta): self
    {
        if (!$this->cartas->contains($carta)) {
            $this->cartas[] = $carta;
            $carta->addFondo($this);
        }

        return $this;
    }

    /**
     * @param Carta $carta
     * @return $this
     */
    public function removeCarta(Carta $carta): self
    {
        if ($this->cartas->removeElement($carta)) {
            $carta->removeFondo($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Investigacion>
     */
    public function getInvestigaciones(): Collection
    {
        return $this->investigaciones;
    }

    /**
     * @param Investigacion $investigacione
     * @return $this
     */
    public function addInvestigacione(Investigacion $investigacione): self
    {
        if (!$this->investigaciones->contains($investigacione)) {
            $this->investigaciones[] = $investigacione;
            $investigacione->setFondo($this);
        }

        return $this;
    }

    /**
     * @param Investigacion $investigacione
     * @return $this
     */
    public function removeInvestigacione(Investigacion $investigacione): self
    {
        if ($this->investigaciones->removeElement($investigacione)) {
            // set the owning side to null (unless already changed)
            if ($investigacione->getFondo() === $this) {
                $investigacione->setFondo(null);
            }
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getContador(): ?int
    {
        return $this->contador;
    }

    /**
     * @param int $contador
     * @return $this
     */
    public function setContador(int $contador): self
    {
        $this->contador = $contador;

        return $this;
    }
}
