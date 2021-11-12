<?php

namespace App\Entity;

use App\Repository\LibroRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LibroRepository::class)
 * @ORM\Table(name="sac_libro")
 */
class Libro
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $tomo;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $anno;

    /**
     * @ORM\Column(type="text")
     */
    private $descripcion;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $identificador;

    /**
     * @ORM\ManyToOne(targetEntity=Deposito::class, inversedBy="libros")
     * @ORM\JoinColumn(nullable=false)
     */
    private $deposito;

    /**
     * @ORM\ManyToOne(targetEntity=Fondo::class, inversedBy="libros")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fondo;

    /**
     * @ORM\ManyToOne(targetEntity=Estante::class, inversedBy="libros")
     * @ORM\JoinColumn(nullable=false)
     */
    private $estante;

    /**
     * @ORM\ManyToOne(targetEntity=Anaquel::class, inversedBy="libros")
     * @ORM\JoinColumn(nullable=false)
     */
    private $anaquel;

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
    public function getTomo(): ?string
    {
        return $this->tomo;
    }

    /**
     * @param string $tomo
     * @return $this
     */
    public function setTomo(string $tomo): self
    {
        $this->tomo = $tomo;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAnno(): ?string
    {
        return $this->anno;
    }

    /**
     * @param string $anno
     * @return $this
     */
    public function setAnno(string $anno): self
    {
        $this->anno = $anno;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    /**
     * @param string $descripcion
     * @return $this
     */
    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

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
     * @return Deposito|null
     */
    public function getDeposito(): ?Deposito
    {
        return $this->deposito;
    }

    /**
     * @param Deposito|null $deposito
     * @return $this
     */
    public function setDeposito(?Deposito $deposito): self
    {
        $this->deposito = $deposito;

        return $this;
    }

    /**
     * @return Fondo|null
     */
    public function getFondo(): ?Fondo
    {
        return $this->fondo;
    }

    /**
     * @param Fondo|null $fondo
     * @return $this
     */
    public function setFondo(?Fondo $fondo): self
    {
        $this->fondo = $fondo;

        return $this;
    }

    /**
     * @return Estante|null
     */
    public function getEstante(): ?Estante
    {
        return $this->estante;
    }

    /**
     * @param Estante|null $estante
     * @return $this
     */
    public function setEstante(?Estante $estante): self
    {
        $this->estante = $estante;

        return $this;
    }

    /**
     * @return Anaquel|null
     */
    public function getAnaquel(): ?Anaquel
    {
        return $this->anaquel;
    }

    /**
     * @param Anaquel|null $anaquel
     * @return $this
     */
    public function setAnaquel(?Anaquel $anaquel): self
    {
        $this->anaquel = $anaquel;

        return $this;
    }
}
