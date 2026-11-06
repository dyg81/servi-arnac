<?php

namespace App\Entity;

use App\Repository\ClienteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClienteRepository::class)
 * @ORM\Table(name="sac_sala_clientes")
 */
class Cliente
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=11, unique=true)
     */
    private $identificacion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $direccion;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telefono;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $correo;

    /**
     * @ORM\ManyToOne(targetEntity=Pais::class, inversedBy="clientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pais;

    /**
     * @ORM\OneToMany(targetEntity=Carta::class, mappedBy="cliente")
     */
    private $cartas;

    /**
     * @ORM\OneToMany(targetEntity=Investigacion::class, mappedBy="cliente")
     */
    private $investigaciones;

    /**
     * @ORM\OneToMany(targetEntity=Transcripcion::class, mappedBy="cliente")
     */
    private $transcripciones;

    /**
     * @ORM\OneToMany(targetEntity=Reprografia::class, mappedBy="cliente")
     */
    private $reprografias;

    /**
     * @ORM\OneToMany(targetEntity=Certificacion::class, mappedBy="cliente")
     */
    private $certificaciones;

    /**
     * @ORM\ManyToOne(targetEntity=CentroLaboral::class, inversedBy="clientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $centro_laboral;

    /**
     * Init the object properties
     */
    public function __construct()
    {
        $this->cartas = new ArrayCollection();
        $this->investigaciones = new ArrayCollection();
        $this->transcripciones = new ArrayCollection();
        $this->reprografias = new ArrayCollection();
        $this->certificaciones = new ArrayCollection();
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
    public function getIdentificacion(): ?string
    {
        return $this->identificacion;
    }

    /**
     * @param string $identificacion
     * @return $this
     */
    public function setIdentificacion(string $identificacion): self
    {
        $this->identificacion = $identificacion;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    /**
     * @param string $direccion
     * @return $this
     */
    public function setDireccion(string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    /**
     * @param string $telefono
     * @return $this
     */
    public function setTelefono($telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCorreo(): ?string
    {
        return $this->correo;
    }

    /**
     * @param string $correo
     * @return $this
     */
    public function setCorreo($correo): self
    {
        $this->correo = $correo;

        return $this;
    }

    /**
     * @return Pais|null
     */
    public function getPais(): ?Pais
    {
        return $this->pais;
    }

    /**
     * @param Pais|null $pais
     * @return $this
     */
    public function setPais(?Pais $pais): self
    {
        $this->pais = $pais;

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
            $carta->setCliente($this);
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
            // set the owning side to null (unless already changed)
            if ($carta->getCliente() === $this) {
                $carta->setCliente(null);
            }
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
            $investigacione->setCliente($this);
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
            if ($investigacione->getCliente() === $this) {
                $investigacione->setCliente(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Transcripcion>
     */
    public function getTranscripciones(): Collection
    {
        return $this->transcripciones;
    }

    /**
     * @param Transcripcion $transcripcione
     * @return $this
     */
    public function addTranscripcione(Transcripcion $transcripcione): self
    {
        if (!$this->transcripciones->contains($transcripcione)) {
            $this->transcripciones[] = $transcripcione;
            $transcripcione->setCliente($this);
        }

        return $this;
    }

    /**
     * @param Transcripcion $transcripcione
     * @return $this
     */
    public function removeTranscripcione(Transcripcion $transcripcione): self
    {
        if ($this->transcripciones->removeElement($transcripcione)) {
            // set the owning side to null (unless already changed)
            if ($transcripcione->getCliente() === $this) {
                $transcripcione->setCliente(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reprografia>
     */
    public function getReprografias(): Collection
    {
        return $this->reprografias;
    }

    /**
     * @param Reprografia $reprografia
     * @return $this
     */
    public function addReprografia(Reprografia $reprografia): self
    {
        if (!$this->reprografias->contains($reprografia)) {
            $this->reprografias[] = $reprografia;
            $reprografia->setCliente($this);
        }

        return $this;
    }

    /**
     * @param Reprografia $reprografia
     * @return $this
     */
    public function removeReprografia(Reprografia $reprografia): self
    {
        if ($this->reprografias->removeElement($reprografia)) {
            // set the owning side to null (unless already changed)
            if ($reprografia->getCliente() === $this) {
                $reprografia->setCliente(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Certificacion>
     */
    public function getCertificaciones(): Collection
    {
        return $this->certificaciones;
    }

    /**
     * @param Certificacion $certificacione
     * @return $this
     */
    public function addCertificacione(Certificacion $certificacione): self
    {
        if (!$this->certificaciones->contains($certificacione)) {
            $this->certificaciones[] = $certificacione;
            $certificacione->setCliente($this);
        }

        return $this;
    }

    /**
     * @param Certificacion $certificacione
     * @return $this
     */
    public function removeCertificacione(Certificacion $certificacione): self
    {
        if ($this->certificaciones->removeElement($certificacione)) {
            // set the owning side to null (unless already changed)
            if ($certificacione->getCliente() === $this) {
                $certificacione->setCliente(null);
            }
        }

        return $this;
    }

    /**
     * @return CentroLaboral|null
     */
    public function getCentroLaboral(): ?CentroLaboral
    {
        return $this->centro_laboral;
    }

    /**
     * @param CentroLaboral|null $centro_laboral
     * @return $this
     */
    public function setCentroLaboral(?CentroLaboral $centro_laboral): self
    {
        $this->centro_laboral = $centro_laboral;

        return $this;
    }
}
