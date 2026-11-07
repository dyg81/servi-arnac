<?php

namespace App\Controller\Sala;

use App\Entity\Carta;
use App\Entity\Cliente;
use App\Entity\Expediente;
use App\Entity\Fondo;
use App\Entity\Investigacion;
use App\Entity\Libro;
use App\Entity\Traza;
use App\Entity\Vitrina;
use App\Form\InvestigacionType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sala")
 */
class InvestigacionController extends AbstractController
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
     * @Route("/listar-investigaciones", name="sala_listar_investigaciones", methods={"GET"})
     * @return Response
     */
    public function listarInvestigaciones(): Response
    {
        return $this->render('sala/investigacion/listar.html.twig', [
            'investigaciones' => $this->entityManager->getRepository(Investigacion::class)->obtenerInvestigacionesPorFechaActualizacion(),
        ]);
    }

    /**
     * @Route("/agregar-investigacion", name="sala_agregar_investigacion", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function agregarInvestigacion(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $investigacion = new Investigacion();
            $form = $this->createForm(InvestigacionType::class, $investigacion);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha solicitado una investigacón para el cliente ' . $investigacion->getCliente());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($investigacion);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Investigación agregada correctamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                }

                return new ModalRedirectResponse($this->generateUrl('sala_listar_investigaciones'));
            }

            return $this->render('sala/investigacion/agregar.html.twig', [
                'investigacion' => $investigacion,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/detalles-investigacion/{id}", name="sala_detalles_investigacion", methods={"GET"})
     * @param Request $request
     * @param Investigacion $investigacion
     * @return Response
     */
    public function verDetallesInvestigacion(Request $request, Investigacion $investigacion): Response
    {
        if ($request->isXmlHttpRequest()) {
            return $this->render('sala/investigacion/detalles.html.twig', array(
                'investigacion' => $investigacion,
            ));
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/entregar-documentos-investigacion/{id}", name="sala_entregar_documentos_investigacion", methods={"POST"})
     * @param Investigacion $investigacion
     * @return Response
     */
    public function entregarDocumentosInvestigacion(Request $request, Investigacion $investigacion): Response
    {
        if (!$this->isCsrfTokenValid('sala_entregar_documentos_investigacion', $request->request->get('_token'))) { throw $this->createAccessDeniedException('Token CSRF inválido.'); }
        $investigacion->setEstado(Investigacion::ESTADO_SALA_CLIENTE);
        $investigacion->setFechaActualizacion(new DateTime());

        foreach ($investigacion->getExpedientes() as $exp) {
            $expediente = $this->entityManager->getRepository(Expediente::class)->find($exp);
            if ($expediente->getEstado() == Expediente::EXP_ESTADO_SOLICITADO_PARA_SALA or $expediente->getEstado() == Expediente::EXP_ESTADO_EN_SALA_RECEPCION) {
                $expediente->setEstado(Expediente::EXP_ESTADO_RESERVADO_EN_SALA);
                $this->entityManager->persist($expediente);
            } elseif ($expediente->getEstado() == Expediente::EXP_ESTADO_RESERVADO_EN_SALA) {
                $investigacion->removeExpediente($exp);
            }
        }

        foreach ($investigacion->getLibros() as $lib) {
            $libro = $this->entityManager->getRepository(Libro::class)->find($lib);
            if ($libro->getEstado() == Libro::LIB_ESTADO_SOLICITADO_PARA_SALA or $libro->getEstado() == Libro::LIB_ESTADO_EN_SALA_RECEPCION) {
                $libro->setEstado(Libro::LIB_ESTADO_RESERVADO_EN_SALA);
                $this->entityManager->persist($libro);
            } elseif ($libro->getEstado() == Libro::LIB_ESTADO_RESERVADO_EN_SALA) {
                $investigacion->removeLibro($lib);
            }
        }

        $traza = new Traza();
        $traza->setTipo('danger');
        $traza->setUsuario($this->getUser()->getNombre());
        $traza->setContenido('Ha entregado al cliente ' . $investigacion->getCliente() . ' los documentos asociados a la investigación');

        $this->entityManager->persist($traza);
        $this->entityManager->persist($investigacion);

        try {
            $this->entityManager->flush();
            $this->addFlash('success', 'Documentos entregados correctamente.');
        } catch (Exception $e) {
            error_log((string) $e);
            $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
        }

        return $this->redirectToRoute('sala_listar_investigaciones');
    }

    /**
     * @Route("/recoger-documentos-investigacion/{id}", name="sala_recoger_documentos_investigacion", methods={"POST"})
     * @param Investigacion $investigacion
     * @return Response
     */
    public function recogerDocumentosInvestigacion(Request $request, Investigacion $investigacion): Response
    {
        if (!$this->isCsrfTokenValid('sala_recoger_documentos_investigacion', $request->request->get('_token'))) { throw $this->createAccessDeniedException('Token CSRF inválido.'); }
        $investigacion->setFechaActualizacion(new DateTime());

        if ($investigacion->getEstado() == Investigacion::ESTADO_PAUSADO) {

            foreach ($investigacion->getExpedientes() as $exp) {
                $expediente = $this->entityManager->getRepository(Expediente::class)->find($exp);

                $expediente->setVitrina(false);
                $expediente->setEstado(Expediente::EXP_ESTADO_EN_SALA_RECEPCION);

                $this->entityManager->persist($expediente);
            }

            foreach ($investigacion->getLibros() as $lib) {
                $libro = $this->entityManager->getRepository(Libro::class)->find($lib);

                $libro->setVitrina(false);
                $libro->setEstado(Libro::LIB_ESTADO_EN_SALA_RECEPCION);

                $this->entityManager->persist($libro);
            }

            foreach ($investigacion->getVitrinas() as $vit) {
                $vitrina = $this->entityManager->getRepository(Vitrina::class)->find($vit);

                $this->entityManager->remove($vitrina);
            }

            $investigacion->clearExpedientes();
            $investigacion->clearLibros();
            $investigacion->setEstado(Investigacion::ESTADO_TERMINADO);
        } else {
            $en_vitrina = false;

            foreach ($investigacion->getExpedientes() as $exp) {
                $expediente = $this->entityManager->getRepository(Expediente::class)->find($exp);

                if ($expediente->getEstado() == Expediente::EXP_ESTADO_RESERVADO_EN_SALA) {
                    if ($expediente->getVitrina()) {
                        $vitrina = $this->entityManager->getRepository(Vitrina::class)->findOneBy(['documento_id' => $expediente->getId(), 'documento_tipo' => 'exp']);
                        if ($vitrina) {
                            $this->entityManager->remove($vitrina);
                        }
                    }

                    $expediente->setVitrina(false);
                    $expediente->setEstado(Expediente::EXP_ESTADO_EN_SALA_RECEPCION);

                    $this->entityManager->persist($expediente);

                    $investigacion->removeExpediente($expediente);
                }

                if ($expediente->getEstado() == Expediente::EXP_ESTADO_EN_VITRINA) {
                    $en_vitrina = true;
                }
            }

            foreach ($investigacion->getLibros() as $lib) {
                $libro = $this->entityManager->getRepository(Libro::class)->find($lib);

                if ($libro->getEstado() == Libro::LIB_ESTADO_RESERVADO_EN_SALA) {
                    if ($libro->getVitrina()) {
                        $vitrina = $this->entityManager->getRepository(Vitrina::class)->findOneBy(['documento_id' => $libro->getId(), 'documento_tipo' => 'lib']);
                        if ($vitrina) {
                            $this->entityManager->remove($vitrina);
                        }
                    }

                    $libro->setVitrina(false);
                    $libro->setEstado(Libro::LIB_ESTADO_EN_SALA_RECEPCION);

                    $this->entityManager->persist($libro);

                    $investigacion->removeLibro($libro);
                }

                if ($libro->getEstado() == Libro::LIB_ESTADO_EN_VITRINA) {
                    $en_vitrina = true;
                }
            }

            if ($en_vitrina) {
                $investigacion->setEstado(Investigacion::ESTADO_PAUSADO);
            } else {
                $investigacion->setEstado(Investigacion::ESTADO_TERMINADO);
            }
        }

        $traza = new Traza();
        $traza->setTipo('warning');
        $traza->setUsuario($this->getUser()->getNombre());
        $traza->setContenido('Ha recogido del cliente ' . $investigacion->getCliente() . ' los documentos asociados a la investigación');

        $this->entityManager->persist($traza);
        $this->entityManager->persist($investigacion);

        try {
            $this->entityManager->flush();
            $this->addFlash('success', 'Documentos recogidos correctamente.');
        } catch (Exception $e) {
            error_log((string) $e);
            $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
        }

        return $this->redirectToRoute('sala_listar_investigaciones');
    }

    /**
     * @Route("/cancelar-investigacion/{id}", name="sala_cancelar_investigacion", methods={"POST"})
     * @param Investigacion $investigacion
     * @return Response
     */
    public function cancelarSolicitudInvestigacion(Request $request, Investigacion $investigacion): Response
    {
        if (!$this->isCsrfTokenValid('sala_cancelar_investigacion', $request->request->get('_token'))) { throw $this->createAccessDeniedException('Token CSRF inválido.'); }
        $investigacion->setEstado(Investigacion::ESTADO_CANCELADO);
        $investigacion->setFechaActualizacion(new DateTime());

        foreach ($investigacion->getExpedientes() as $exp) {
            $expediente = $this->entityManager->getRepository(Expediente::class)->find($exp);
            if ($expediente->getEstado() == Expediente::EXP_ESTADO_SOLICITADO_PARA_SALA) {
                $expediente->setEstado(Expediente::EXP_ESTADO_EN_SALA_RECEPCION);
                $this->entityManager->persist($expediente);
            }
        }

        foreach ($investigacion->getLibros() as $lib) {
            $libro = $this->entityManager->getRepository(Libro::class)->find($lib);
            if ($libro->getEstado() == Libro::LIB_ESTADO_SOLICITADO_PARA_SALA) {
                $libro->setEstado(Libro::LIB_ESTADO_EN_SALA_RECEPCION);
                $this->entityManager->persist($libro);
            }
        }

        $investigacion->clearExpedientes();
        $investigacion->clearLibros();

        $traza = new Traza();
        $traza->setTipo('danger');
        $traza->setUsuario($this->getUser()->getNombre());
        $traza->setContenido('Ha cancelado la investigación asociada al cliente ' . $investigacion->getCliente());

        $this->entityManager->persist($traza);
        $this->entityManager->persist($investigacion);

        try {
            $this->entityManager->flush();
            $this->addFlash('success', 'Investigación cancelada correctamente.');
        } catch (Exception $e) {
            error_log((string) $e);
            $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
        }

        return $this->redirectToRoute('sala_listar_investigaciones');
    }

    /**
     * @Route("/obtener-clientes-investigacion", name="obtener_clientes_investigacion", condition="request.isXmlHttpRequest()")
     * @return JsonResponse
     */
    public function obtenerClientesInvestigacion(): JsonResponse
    {
        $clientes = $this->entityManager->getRepository(Cliente::class)->obtenerClientesAutorizados();

        $clientesArray = array();

        foreach ($clientes as $cliente) {
            $inv_activa = false;
            $investigaciones = $cliente->getInvestigaciones();

            foreach ($investigaciones as $investigacion) {
                if (($investigacion->getEstado() != Investigacion::ESTADO_TERMINADO) and ($investigacion->getEstado() != Investigacion::ESTADO_CANCELADO)) {
                    $inv_activa = true;
                }
            }

            if (!($inv_activa)) {
                $clientesArray[] = array(
                    "id" => $cliente->getId(),
                    "nombre" => $cliente->getNombre(),
                );
            }
        }

        return new JsonResponse($clientesArray);
    }

    /**
     * @Route("/obtener-fondos-investigacion", name="obtener_fondos_investigacion", condition="request.isXmlHttpRequest()")
     * @param Request $request
     * @return JsonResponse
     */
    public function obtenerFondosInvestigacion(Request $request): JsonResponse
    {
        $fondosArray = array();

        $cartas = $this->entityManager->getRepository(Carta::class)->obtenerCartasActivasDelCliente($request->query->get('clienteid'));

        foreach ($cartas as $carta) {
            $fondos = $this->entityManager->getRepository(Carta::class)->obtenerFondosPorCartaYCliente($carta, $request->query->get('clienteid'));

            foreach ($fondos as $fondo) {
                $fondosArray[] = array(
                    "id" => $fondo->getId(),
                    "numero" => $fondo->getNombre(),
                );
            }
        }

        return new JsonResponse($fondosArray);
    }

    /**
     * @Route("/obtener-legajos-investigacion", name="obtener_legajos_investigacion", condition="request.isXmlHttpRequest()")
     * @param Request $request
     * @return JsonResponse
     */
    public function obtenerLegajosInvestigacion(Request $request): JsonResponse
    {
        $fondo = $this->entityManager->getRepository(Fondo::class)->find($request->query->get('fondoid'));

        $legajosArray = array();

        foreach ($fondo->getExpedientes() as $expediente) {
            $legajosArray[] = array(
                "id" => $expediente->getLegajo()->getId(),
                "numero" => $expediente->getLegajo()->getNumero(),
            );
        }

        $legajosArray = array_unique($legajosArray, SORT_REGULAR);

        array_multisort(
            array_column($legajosArray, 'numero'),
            SORT_ASC,
            $legajosArray
        );

//        $i = 0;
//        $temp_array = array();
//        $key_array = array();
//
//        foreach($legajosArray as $legajo) {
//            if (!in_array($legajo['id'], $key_array)) {
//                $key_array[$i] = $legajo['id'];
//                $temp_array[$i] = $legajo;
//            }
//            $i++;
//        }

        return new JsonResponse($legajosArray);
    }

    /**
     * @Route("/obtener-expedientes-investigacion", name="obtener_expedientes_investigacion", condition="request.isXmlHttpRequest()")
     * @param Request $request
     * @return JsonResponse
     */
    public function obtenerExpedientesInvestigacion(Request $request): JsonResponse
    {
        $expedientes = $this->entityManager->getRepository(Expediente::class)->obtenerExpedientesPorFondoYLegajo($request->query->get('fondoid'), $request->query->get('legajoid'));

        $expedientesArray = array();

        foreach ($expedientes as $expediente) {
            $expedientesArray[] = array(
                "id" => $expediente->getId(),
                "numero" => 'EXP_' . $expediente->getNumero(),
            );
        }

        return new JsonResponse($expedientesArray);
    }

    /**
     * @Route("/obtener-libros-investigacion", name="obtener_libros_investigacion", condition="request.isXmlHttpRequest()")
     * @param Request $request
     * @return JsonResponse
     */
    public function obtenerLibrosInvestigacion(Request $request): JsonResponse
    {
        $libros = $this->entityManager->getRepository(Libro::class)->obtenerLibrosPorFondo($request->query->get('fondoid'));

        $librosArray = array();

        foreach ($libros as $libro) {
            $librosArray[] = array(
                "id" => $libro->getId(),
                "numero" => $libro->getAnno() . ' (Tomo: ' . $libro->getTomo() . ')',
            );
        }

        return new JsonResponse($librosArray);
    }
}
