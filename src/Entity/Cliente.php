<?php

namespace App\Entity;

use App\Repository\ClienteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClienteRepository::class)
 * @ORM\Table(name="sac_cliente")
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
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $identificacion;

    /**
     * @ORM\ManyToOne(targetEntity=Pais::class, inversedBy="clientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pais;

    /**
     * @ORM\ManyToOne(targetEntity=Categoria::class, inversedBy="clientes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categoria;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $direccion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telefono;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $correo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ocupacion;

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
     * @return Categoria|null
     */
    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    /**
     * @param Categoria|null $categoria
     * @return $this
     */
    public function setCategoria(?Categoria $categoria): self
    {
        $this->categoria = $categoria;

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
    public function setTelefono(string $telefono): self
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
    public function setCorreo(string $correo): self
    {
        $this->correo = $correo;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOcupacion(): ?string
    {
        return $this->ocupacion;
    }

    /**
     * @param string $ocupacion
     * @return $this
     */
    public function setOcupacion(string $ocupacion): self
    {
        $this->ocupacion = $ocupacion;

        return $this;
    }
}
