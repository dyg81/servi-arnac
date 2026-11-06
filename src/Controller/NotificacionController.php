<?php

namespace App\Controller;

use App\Entity\Carta;
use App\Entity\Certificacion;
use App\Entity\Investigacion;
use App\Entity\Reprografia;
use App\Entity\Transcripcion;
use App\Entity\Vitrina;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class NotificacionController extends AbstractController
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
     * @Route("/sala/notificaciones", name="sala_notificaciones", methods={"GET"})
     * @return JsonResponse
     */
    public function infoNotificacionesSala(): JsonResponse
    {
        $investigacionesProcesadas = $this->entityManager->getRepository(Investigacion::class)->obtenerInvestigacionesEstadoProcesadas();
        $investigacionesCanceladas = $this->entityManager->getRepository(Investigacion::class)->obtenerInvestigacionesCanceladasHace5Min();
        $vitrinasVencidas = $this->entityManager->getRepository(Vitrina::class)->obtenerVitrinasVencidas();
        $cartasProcesadas = $this->entityManager->getRepository(Carta::class)->obtenerUltimasCartasProcesadas();

        $data = array();
        $data[0] = count($investigacionesProcesadas) + count($investigacionesCanceladas);
        $data[1] = count($vitrinasVencidas);
        $data[2] = count($cartasProcesadas);

        return new JsonResponse($data);
    }

    /**
     * @Route("/estacionario/notificaciones", name="estacionario_notificaciones", methods={"GET"})
     * @return JsonResponse
     */
    public function infoNotificacionesEstacionario(): JsonResponse
    {
        $investigacionesSolicitadas = $this->entityManager->getRepository(Investigacion::class)->obtenerInvestigacionesSolicitadas();

        $data = array();
        $data[0] = count($investigacionesSolicitadas);

        return new JsonResponse($data);
    }

    /**
     * @Route("/direccion/notificaciones", name="direccion_notificaciones", methods={"GET"})
     * @return JsonResponse
     */
    public function infoNotificacionesDireccion(): JsonResponse
    {
        $cartasPendientes = $this->entityManager->getRepository(Carta::class)->obtenerCartasSolicitadas();

        $data = array();
        $data[0] = count($cartasPendientes);

        return new JsonResponse($data);
    }

    /**
     * @Route("/transcripcion/notificaciones", name="transcripcion_notificaciones", methods={"GET"})
     * @return JsonResponse
     */
    public function infoNotificacionesTranscripcion(): JsonResponse
    {
        $transcripcionesNuevas = $this->entityManager->getRepository(Transcripcion::class)->obtenerTranscripcionesSolicitadas();

        $data = array();
        $data[0] = count($transcripcionesNuevas);

        return new JsonResponse($data);
    }

    /**
     * @Route("/reprografia/notificaciones", name="reprografia_notificaciones", methods={"GET"})
     * @return JsonResponse
     */
    public function infoNotificacionesReprografia(): JsonResponse
    {
        $reprografiasNuevas = $this->entityManager->getRepository(Reprografia::class)->obtenerReprografiasSolicitadas();

        $data = array();
        $data[0] = count($reprografiasNuevas);

        return new JsonResponse($data);
    }

    /**
     * @Route("/certificacion/notificaciones", name="certificacion_notificaciones", methods={"GET"})
     * @return JsonResponse
     */
    public function infoNotificacionesCertificacion(): JsonResponse
    {
        $certificacionesNuevas = $this->entityManager->getRepository(Certificacion::class)->obtenerCertificacionesSolicitadas();

        $data = array();
        $data[0] = count($certificacionesNuevas);

        return new JsonResponse($data);
    }
}
