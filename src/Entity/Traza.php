<?php

namespace App\Entity;

use App\Repository\TrazasRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrazasRepository::class)
 * @ORM\Table(name="sac_trazas")
 */
class Traza
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * Historical username text; intentionally not a Doctrine relation because
     * the database stores a name snapshot and has no user foreign key.
     *
     * @ORM\Column(type="string")
     */
    private $usuario;

    /**
     * @ORM\Column(type="text")
     */
    private $contenido;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $tipo;

    /**
     * Init the object properties
     */
    public function __construct()
    {
        $this->fecha = new DateTime();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getFecha(): ?DateTimeInterface
    {
        return $this->fecha;
    }

    /**
     * @param DateTimeInterface $fecha
     * @return $this
     */
    public function setFecha(DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    /**
     * @param string $usuario
     * @return $this
     */
    public function setUsuario(string $usuario): self
    {
        $this->usuario = $usuario;

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
     * @return string|null
     */
    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    /**
     * @param string $tipo
     * @return $this
     */
    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }
}
