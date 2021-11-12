<?php

namespace App\Entity;

use App\Repository\ExpedienteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExpedienteRepository::class)
 * @ORM\Table(name="sac_expediente")
 */
class Expediente
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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $identificador;

    /**
     * @ORM\ManyToOne(targetEntity=Deposito::class, inversedBy="expedientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $deposito;

    /**
     * @ORM\ManyToOne(targetEntity=Fondo::class, inversedBy="expedientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fondo;

    /**
     * @ORM\ManyToOne(targetEntity=Legajo::class, inversedBy="expedientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $legajo;

    /**
     * @ORM\ManyToOne(targetEntity=Estante::class, inversedBy="expedientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $estante;

    /**
     * @ORM\ManyToOne(targetEntity=Anaquel::class, inversedBy="expedientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $anaquel;

    /**
     * @ORM\Column(type="text")
     */
    private $descripcion;

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
     * @return deposito|null
     */
    public function getDeposito(): ?deposito
    {
        return $this->deposito;
    }

    /**
     * @param deposito|null $deposito
     * @return $this
     */
    public function setDeposito(?deposito $deposito): self
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
     * @return legajo|null
     */
    public function getLegajo(): ?legajo
    {
        return $this->legajo;
    }

    /**
     * @param legajo|null $legajo
     * @return $this
     */
    public function setLegajo(?legajo $legajo): self
    {
        $this->legajo = $legajo;

        return $this;
    }

    /**
     * @return estante|null
     */
    public function getEstante(): ?estante
    {
        return $this->estante;
    }

    /**
     * @param estante|null $estante
     * @return $this
     */
    public function setEstante(?estante $estante): self
    {
        $this->estante = $estante;

        return $this;
    }

    /**
     * @return anaquel|null
     */
    public function getAnaquel(): ?anaquel
    {
        return $this->anaquel;
    }

    /**
     * @param anaquel|null $anaquel
     * @return $this
     */
    public function setAnaquel(?anaquel $anaquel): self
    {
        $this->anaquel = $anaquel;

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
}