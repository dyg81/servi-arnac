<?php

namespace App\Controller\Sala;

use App\Entity\Expediente;
use App\Entity\Investigacion;
use App\Entity\Libro;
use App\Entity\Traza;
use App\Entity\Vitrina;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sala")
 */
class VitrinaController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/listar-documentos-en-vitrina", name="sala_listar_documentos_en_vitrina")
     * @return Response
     */
    public function listarDocumentoEnVitrina(): Response
    {
        return $this->render('sala/listar-vitrinas.html.twig', [
            'vitrinas' => $this->entityManager->getRepository(Vitrina::class)->findAll(),
        ]);
    }

    /**
     * @Route("/archivar-en-vitrina/{investigacion}/{documento}/{tipo}", name="sala_archivar_documento_en_vitrina", methods={"POST"})
     * @param Investigacion $investigacion
     * @param int $documento
     * @param string $tipo
     * @return RedirectResponse
     */
    public function archivarEnVitrina(Request $request, Investigacion $investigacion, int $documento, string $tipo): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('sala_archivar_documento_en_vitrina', $request->request->get('_token'))) { throw $this->createAccessDeniedException('Token CSRF inválido.'); }
        if ($tipo === 'expediente') {
            $expediente = $this->entityManager->getRepository(Expediente::class)->find($documento);
            if (!$expediente || !$investigacion->getExpedientes()->contains($expediente)) { throw $this->createAccessDeniedException('El documento no pertenece a esta investigación.'); }

            if ($this->entityManager->getRepository(Vitrina::class)->findOneBy(['documento_id' => $documento, 'documento_tipo' => 'exp'])) {
                $expediente->setEstado(Expediente::EXP_ESTADO_EN_VITRINA);
            } else {
                $vitrina = new Vitrina();
                $vitrina->setInvestigacion($investigacion);
                $vitrina->setDocumentoId($documento);
                $vitrina->setDocumentoTipo('exp');
                $vitrina->setDocumentoNombre('Expediente ' . $expediente->getNumero());

                $expediente->setVitrina(true);
                $expediente->setEstado(Expediente::EXP_ESTADO_EN_VITRINA);

                $this->entityManager->persist($expediente);
                $this->entityManager->persist($vitrina);
            }
        } elseif ($tipo === 'libro') {
            $libro = $this->entityManager->getRepository(Libro::class)->find($documento);
            if (!$libro || !$investigacion->getLibros()->contains($libro)) { throw $this->createAccessDeniedException('El documento no pertenece a esta investigación.'); }
            if ($this->entityManager->getRepository(Vitrina::class)->findOneBy(['documento_id' => $documento, 'documento_tipo' => 'lib'])) {
                $libro->setEstado(Libro::LIB_ESTADO_EN_VITRINA);
            } else {
                $vitrina = new Vitrina();
                $vitrina->setInvestigacion($investigacion);
                $vitrina->setDocumentoId($documento);
                $vitrina->setDocumentoTipo('lib');
                $vitrina->setDocumentoNombre('Libro ' . $libro->getAnno() . ' (Tomo: ' . $libro->getTomo() . ')');

                $libro->setVitrina(true);
                $libro->setEstado(Libro::LIB_ESTADO_EN_VITRINA);

                $this->entityManager->persist($libro);
                $this->entityManager->persist($vitrina);
            }
        } else { throw $this->createNotFoundException(); }

        $date = $investigacion->getFechaActualizacion();

        $investigacion->setEstado(Investigacion::ESTADO_PAUSADO);
        $investigacion->setFechaActualizacion(new DateTime());

        foreach ($investigacion->getExpedientes() as $e) {
            $exp = $this->entityManager->getRepository(Expediente::class)->find($e);

            if ($exp->getEstado() == Expediente::EXP_ESTADO_RESERVADO_EN_SALA) {
                $investigacion->setEstado(Investigacion::ESTADO_SALA_CLIENTE);
                $investigacion->setFechaActualizacion($date);
            }
        }

        foreach ($investigacion->getLibros() as $l) {
            $lib = $this->entityManager->getRepository(Libro::class)->find($l);

            if ($lib->getEstado() == Libro::LIB_ESTADO_RESERVADO_EN_SALA) {
                $investigacion->setEstado(Investigacion::ESTADO_SALA_CLIENTE);
                $investigacion->setFechaActualizacion($date);
            }
        }

        $traza = new Traza();
        $traza->setTipo('warning');
        $traza->setUsuario($this->getUser()->getNombre());
        $traza->setContenido('Ha archivado en vitrina un documento para el cliente ' . $investigacion->getCliente());

        $this->entityManager->persist($traza);
        $this->entityManager->persist($investigacion);

        try {
            $this->entityManager->flush();
            $this->addFlash('success', 'Documento pasado a vitrina correctamente.');
        } catch (Exception $e) {
            error_log((string) $e);
            $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
        }

        return $this->redirectToRoute('sala_listar_investigaciones');
    }

    /**
     * @Route("/regresar-de-vitrina/{investigacion}/{documento}/{tipo}", name="sala_regresar_documento_de_vitrina", methods={"POST"})
     * @param Investigacion $investigacion
     * @param int $documento
     * @param string $tipo
     * @return RedirectResponse
     */
    public function regresarDeVitrina(Request $request, Investigacion $investigacion, int $documento, string $tipo): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('sala_regresar_documento_de_vitrina', $request->request->get('_token'))) { throw $this->createAccessDeniedException('Token CSRF inválido.'); }
        if ($investigacion->getEstado() == Investigacion::ESTADO_PAUSADO) {
            $investigacion->setFechaActualizacion(new DateTime());
        }

        $investigacion->setEstado(Investigacion::ESTADO_SALA_CLIENTE);

        if ($tipo == 'expediente') {
            $expediente = $this->entityManager->getRepository(Expediente::class)->find($documento);
            if (!$expediente || !$investigacion->getExpedientes()->contains($expediente)) { throw $this->createAccessDeniedException('El documento no pertenece a esta investigación.'); }
            $expediente->setEstado(Expediente::EXP_ESTADO_RESERVADO_EN_SALA);

            $this->entityManager->persist($expediente);
        } elseif ($tipo == 'libro') {
            $libro = $this->entityManager->getRepository(Libro::class)->find($documento);
            if (!$libro || !$investigacion->getLibros()->contains($libro)) { throw $this->createAccessDeniedException('El documento no pertenece a esta investigación.'); }
            $libro->setEstado(Libro::LIB_ESTADO_RESERVADO_EN_SALA);

            $this->entityManager->persist($libro);
        } else {
            throw $this->createNotFoundException('Tipo de documento no reconocido.');
        }

        $traza = new Traza();
        $traza->setTipo('warning');
        $traza->setUsuario($this->getUser()->getNombre());
        $traza->setContenido('Ha entregado al cliente ' . $investigacion->getCliente() . ' un documento previamente archivado en vitrina');

        $this->entityManager->persist($traza);
        $this->entityManager->persist($investigacion);

        try {
            $this->entityManager->flush();
            $this->addFlash('success', 'Documento recuperado de vitrina correctamente.');
        } catch (Exception $e) {
            error_log((string) $e);
            $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
        }

        return $this->redirectToRoute('sala_listar_investigaciones');
    }

    /**
     * @Route("/eliminar-documento-de-vitrina/{vitrina}", name="sala_eliminar_documento_de_vitrina", methods={"POST"})
     * @param Vitrina $vitrina
     * @return RedirectResponse|Response
     */
    public function eliminarDeVitrina(Request $request, Vitrina $vitrina)
    {
        if (!$this->isCsrfTokenValid('sala_eliminar_documento_de_vitrina', $request->request->get('_token'))) { throw $this->createAccessDeniedException('Token CSRF inválido.'); }
        if ($vitrina->getDocumentoTipo() === 'exp') {
            $expediente = $this->entityManager->getRepository(Expediente::class)->findOneBy(['id' => $vitrina->getDocumentoId()]);
            $investigacion = $vitrina->getInvestigacion();
            if (!$expediente || !$investigacion->getExpedientes()->contains($expediente)) { throw $this->createAccessDeniedException('El documento no pertenece a esta investigación.'); }
            $expediente->setVitrina(false);
            $expediente->setEstado(Expediente::EXP_ESTADO_EN_SALA_RECEPCION);

            $this->entityManager->persist($expediente);

            $investigacion->removeExpediente($expediente);
        } elseif ($vitrina->getDocumentoTipo() === 'lib') {
            $libro = $this->entityManager->getRepository(Libro::class)->findOneBy(['id' => $vitrina->getDocumentoId()]);
            $investigacion = $vitrina->getInvestigacion();
            if (!$libro || !$investigacion->getLibros()->contains($libro)) { throw $this->createAccessDeniedException('El documento no pertenece a esta investigación.'); }
            $libro->setVitrina(false);
            $libro->setEstado(Libro::LIB_ESTADO_EN_SALA_RECEPCION);

            $this->entityManager->persist($libro);

            $investigacion = $vitrina->getInvestigacion();
            $investigacion->removeLibro($libro);
        } else {
            throw $this->createNotFoundException();
        }

        if (count($investigacion->getExpedientes()) == 0 and count($investigacion->getLibros()) == 0) {
            $investigacion->setEstado(Investigacion::ESTADO_TERMINADO);
        }

        $this->entityManager->remove($vitrina);
        $this->entityManager->persist($investigacion);

        try {
            $this->entityManager->flush();
            $this->addFlash('success', 'Documento recogido correctamente.');
        } catch (Exception $e) {
            error_log((string) $e);
            $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
        }

        return $this->redirectToRoute('sala_listar_documentos_en_vitrina');
    }
}
