<?php

namespace App\Service;

use App\Entity\Expediente;
use App\Entity\Investigacion;
use App\Entity\Libro;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Centraliza la comprobación de que un documento solicitado pertenece
 * a la investigación que lo presenta.
 */
class DocumentoInvestigacionValidator
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function libro(int $id, Investigacion $investigacion): Libro
    {
        $libro = $this->entityManager->getRepository(Libro::class)->find($id);
        if (!$libro || !$investigacion->getLibros()->contains($libro)) {
            throw new AccessDeniedHttpException('El documento no pertenece a esta investigación.');
        }

        return $libro;
    }

    public function expediente(int $id, Investigacion $investigacion): Expediente
    {
        $expediente = $this->entityManager->getRepository(Expediente::class)->find($id);
        if (!$expediente || !$investigacion->getExpedientes()->contains($expediente)) {
            throw new AccessDeniedHttpException('El documento no pertenece a esta investigación.');
        }

        return $expediente;
    }
}
