<?php

namespace App\Entity;

use App\Repository\LegajoRepository;
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
     * @ORM\Column(type="string", length=6)
     */
    private $identificador;

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
}
