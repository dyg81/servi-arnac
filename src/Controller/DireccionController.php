<?php

namespace App\Controller;

use App\Entity\Carta;
use App\Entity\Certificacion;
use App\Entity\Cliente;
use App\Entity\Expediente;
use App\Entity\Fondo;
use App\Entity\Investigacion;
use App\Entity\Libro;
use App\Entity\Reprografia;
use App\Entity\Transcripcion;
use App\Entity\Traza;
use App\Entity\Usuario;
use App\Form\CartaType;
use App\Service\FileUploader;
use App\Form\UsuarioType;
use App\Repository\CartaRepository;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/direccion")
 */
class DireccionController extends AbstractController
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
     * @Route("/estadisticas", name="direccion_estadisticas")
     * @return Response
     */
    public function estadisticas(): Response
    {
        $mes = '';
        $recurrences = 12;
        $data_anual = array();
        $start = new DateTime();
        $interval = new DateInterval('P1M');

        $start->setDate($start->format('Y'), $start->format('n'), 1);
        $start->setTime(0, 0);
        $start->sub(new DateInterval('P12M'));

        foreach (new DatePeriod($start, $interval, $recurrences, true) as $date) {
            switch ($date->format('M')) {
                case 'Jan':
                    $mes = 'Ene' . strftime($date->format('y'));
                    break;
                case 'Feb':
                    $mes = 'Feb' . strftime($date->format('y'));
                    break;
                case 'Mar':
                    $mes = 'Mar' . strftime($date->format('y'));
                    break;
                case 'Apr':
                    $mes = 'Abr' . strftime($date->format('y'));
                    break;
                case 'May':
                    $mes = 'May' . strftime($date->format('y'));
                    break;
                case 'Jun':
                    $mes = 'Jun' . strftime($date->format('y'));
                    break;
                case 'Jul':
                    $mes = 'Jul' . strftime($date->format('y'));
                    break;
                case 'Aug':
                    $mes = 'Ago' . strftime($date->format('y'));
                    break;
                case 'Sep':
                    $mes = 'Sep' . strftime($date->format('y'));
                    break;
                case 'Oct':
                    $mes = 'Oct' . strftime($date->format('y'));
                    break;
                case 'Nov':
                    $mes = 'Nov' . strftime($date->format('y'));
                    break;
                case 'Dec':
                    $mes = 'Dic' . strftime($date->format('y'));
                    break;
            }

            $data_anual[] = array(
                'mes' => $mes,
                'investigaciones' => $this->entityManager->getRepository(Investigacion::class)->contarPorMes($date),
                'transcripciones' => $this->entityManager->getRepository(Transcripcion::class)->contarPorMes($date),
                'reprografias' => $this->entityManager->getRepository(Reprografia::class)->contarPorMes($date),
                'certificaciones' => $this->entityManager->getRepository(Certificacion::class)->contarPorMes($date),
            );
        }

        return $this->render('direccion/estadisticas.html.twig', array(
            'fondos_totales' => $this->entityManager->getRepository(Fondo::class)->count([]),
            'expedientes_totales' => $this->entityManager->getRepository(Expediente::class)->count([]),
            'libros_totales' => $this->entityManager->getRepository(Libro::class)->count([]),
            'clientes_totales' => $this->entityManager->getRepository(Cliente::class)->count([]),
            'investigaciones_totales' => $this->entityManager->getRepository(Investigacion::class)->count([]),
            'transcripciones_totales' => $this->entityManager->getRepository(Transcripcion::class)->count([]),
            'reprografias_totales' => $this->entityManager->getRepository(Reprografia::class)->count([]),
            'certificaciones_totales' => $this->entityManager->getRepository(Certificacion::class)->count([]),
            'fondos_mas_usados' => $this->entityManager->getRepository(Fondo::class)->obtenerFondosMasUsados(),
            'investigaciones_del_dia' => $this->entityManager->getRepository(Investigacion::class)->obtenerInvestigacionesDelDia(),
            'transcripciones_del_dia' => $this->entityManager->getRepository(Transcripcion::class)->obtenerTranscripcionesDelDia(),
            'reprografias_del_dia' => $this->entityManager->getRepository(Reprografia::class)->obtenerReprografiasDelDia(),
            'certificaciones_del_dia' => $this->entityManager->getRepository(Certificacion::class)->obtenerCertificacionesDelDia(),
            'expedientes_en_vitrina' => $this->entityManager->getRepository(Expediente::class)->obtenerExpedientesEnVitrina(),
            'libros_en_vitrina' => $this->entityManager->getRepository(Libro::class)->obtenerLibrosEnVitrina(),
            'data_anual' => $data_anual,
        ));
    }

    /**
     * @Route("/reportes", name="direccion_reportes")
     * @return Response
     */
    public function reportes(): Response
    {
        $fondos = [];
        $expedientesPorFondoDeposito = $this->contadoresPorFondoYDeposito(
            $this->entityManager->getRepository(Expediente::class)->contarPorFondoYDeposito()
        );
        $librosPorFondoDeposito = $this->contadoresPorFondoYDeposito(
            $this->entityManager->getRepository(Libro::class)->contarPorFondoYDeposito()
        );

        foreach ($this->entityManager->getRepository(Fondo::class)->findAll() as $fondo) {
            foreach ($fondo->getDepositos() as $deposito) {
                $fondos[] = array(
                    'nombre' => $fondo->getNombre(),
                    'deposito' => 'DNAVE' . $deposito->getNumero(),
                    'expedientes' => $expedientesPorFondoDeposito[$fondo->getId() . ':' . $deposito->getId()] ?? 0,
                    'libros' => $librosPorFondoDeposito[$fondo->getId() . ':' . $deposito->getId()] ?? 0
                );
            }
        }

        //dd($fondos);

        return $this->render('direccion/reportes.html.twig', [
            'clientes' => $this->entityManager->getRepository(Cliente::class)->findAll(),
            'fondos' => $fondos
        ]);
    }

    private function contadoresPorFondoYDeposito(array $filas): array
    {
        $contadores = [];

        foreach ($filas as $fila) {
            $clave = $fila['fondo_id'] . ':' . $fila['deposito_id'];
            $contadores[$clave] = (int) $fila['cantidad'];
        }

        return $contadores;
    }

    /**
     * @Route("/servicios-por-fecha", name="direccion_servicios_por_fecha")
     * @throws Exception
     */
    public function serviciosPorFecha(Request $request): JsonResponse
    {
        $begin = new DateTime($request->query->get('fecha_inicio'));
        $end = new DateTime($request->query->get('fecha_final'));
        $end = $end->modify( '+1 day' );

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        $dates = [];

        foreach ($period as $date) {
            $dates[] = array(
                'fecha' => date_format($date, 'd-m-Y'),
                'inves' => count($this->entityManager->getRepository(Investigacion::class)->obtenerInvestigacionesPorDia($date)),
                'trans' => count($this->entityManager->getRepository(Transcripcion::class)->obtenerTranscripcionesPorDia($date)),
                'repro' => count($this->entityManager->getRepository(Reprografia::class)->obtenerReprografiasPorDia($date)),
                'certi' => count($this->entityManager->getRepository(Certificacion::class)->obtenerCertificacionesPorDia($date))
            );
        }

        return new JsonResponse($dates);
    }

    /**
     * @Route("/servicios-por-cliente", name="direccion_servicios_por_cliente")
     * @param Request $request
     * @return JsonResponse
     */
    public function serviciosPorCliente(Request $request): JsonResponse
    {
        $servicios = [];

        $investigaciones = $this->entityManager->getRepository(Investigacion::class)->findBy(['cliente' => $request->query->get('clienteid')]);

        foreach ($investigaciones as $investigacion) {
            $servicios[] = array(
                'fecha' => date_format($investigacion->getFechaSolicitud(), 'Y-m-d'),
                'servicio' => 'Investigación',
                'cantidad' => count($this->entityManager->getRepository(Investigacion::class)->obtenerInvestigacionesPorFechaYCliente($investigacion->getFechaSolicitud(), $request->query->get('clienteid')))
            );
        }

        $transcripciones = $this->entityManager->getRepository(Transcripcion::class)->findBy(['cliente' => $request->query->get('clienteid')]);

        foreach ($transcripciones as $transcripcion) {
            $servicios[] = array(
                'fecha' => date_format($transcripcion->getFechaSolicitud(), 'Y-m-d'),
                'servicio' => 'Transcripción',
                'cantidad' => count($this->entityManager->getRepository(Transcripcion::class)->obtenerTranscripcionesPorFechaYCliente($transcripcion->getFechaSolicitud(), $request->query->get('clienteid')))
            );
        }

        $reprografias = $this->entityManager->getRepository(Reprografia::class)->findBy(['cliente' => $request->query->get('clienteid')]);

        foreach ($reprografias as $reprografia) {
            $servicios[] = array(
                'fecha' => date_format($reprografia->getFechaSolicitud(), 'Y-m-d'),
                'servicio' => 'Reprografía',
                'cantidad' => count($this->entityManager->getRepository(Reprografia::class)->obtenerReprografiasPorFechaYCliente($reprografia->getFechaSolicitud(), $request->query->get('clienteid')))
            );
        }

        $certificaciones = $this->entityManager->getRepository(Certificacion::class)->findBy(['cliente' => $request->query->get('clienteid')]);

        foreach ($certificaciones as $certificacion) {
            $servicios[] = array(
                'fecha' => date_format($certificacion->getFechaSolicitud(), 'Y-m-d'),
                'servicio' => 'Certificación',
                'cantidad' => count($this->entityManager->getRepository(Certificacion::class)->obtenerCertificacionesPorFechaYCliente($certificacion->getFechaSolicitud(), $request->query->get('clienteid')))
            );
        }

        $servicios = array_unique($servicios, SORT_REGULAR);

        array_multisort(
            array_column($servicios, 'fecha'),
            SORT_ASC,
            $servicios
        );

        return new JsonResponse($servicios);
    }

    /**
     * @Route("/listar-cartas-pendientes", name="direccion_listar_cartas_pendientes")
     * @param CartaRepository $cartaRepository
     * @return Response
     */
    public function listarCartasPendientes(CartaRepository $cartaRepository): Response
    {
        return $this->render('direccion/cartas_pendientes.html.twig', [
            'cartas' => $cartaRepository->obtenerCartasSolicitadas(),
        ]);
    }

    /**
     * @Route("/listar-cartas-respondidas", name="direccion_listar_cartas_respondidas")
     * @param CartaRepository $cartaRepository
     * @return Response
     */
    public function listarCartasRespondidas(CartaRepository $cartaRepository): Response
    {
        return $this->render('direccion/cartas_respondidas.html.twig', [
            'cartas' => $cartaRepository->obtenerCartasRespondidas(),
        ]);
    }

    /**
     * @Route("/aprobar-carta/{id}", name="direccion_aprobar_carta_pendiente", methods={"GET", "POST"})
     * @param Request $request
     * @param Carta $carta
     * @return Response
     */
    public function aprobarCarta(Request $request, Carta $carta): Response
    {
        if ($request->isXmlHttpRequest()) {
            $form = $this->createForm(CartaType::class, $carta);
            $form->remove('cliente');
            $form->remove('documento_file');
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $carta->setFechaRespuesta(new DateTime());
                $carta->setEstado(Carta::CARTA_APROBADA);

                $traza = new Traza();

                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha aprobado la solicitud de autorizo del cliente ' . $carta->getCliente());

                $this->entityManager->persist($traza);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'La carta ha sido aprobada satisfactoriamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                }

                return new ModalRedirectResponse($this->generateUrl('direccion_listar_cartas_pendientes'));
            }

            return $this->render('direccion/aprobar_carta.html.twig', [
                'carta' => $carta,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/denegar-carta/{id}", name="direccion_denegar_carta_pendiente", methods={"GET", "POST"})
     * @param Request $request
     * @param Carta $carta
     * @return Response
     */
    public function denegarCarta(Request $request, Carta $carta): Response
    {
        if ($request->isXmlHttpRequest()) {
            $form = $this->createForm(CartaType::class, $carta);
            $form->remove('cliente');
            $form->remove('documento_file');
            $form->remove('fondos');
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $carta->setFechaRespuesta(new DateTime('now'));
                $carta->setEstado(Carta::CARTA_DENEGADA);

                $traza = new Traza();

                $traza->setTipo('danger');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha denegado la solicitud de autorizo del cliente ' . $carta->getCliente());

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'La carta ha sido denegada satisfactoriamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                }

                return new ModalRedirectResponse($this->generateUrl('direccion_listar_cartas_pendientes'));
            }

            return $this->render('direccion/denegar_carta.html.twig', [
                'carta' => $carta,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/ver-fondos/{id}", name="direccion_ver_fondos")
     * @param Request $request
     * @param Carta $carta
     * @return Response
     */
    public function verFondos(Request $request, Carta $carta): Response
    {
        if ($request->isXmlHttpRequest()) {
            return $this->render('direccion/fondos.html.twig', [
                'carta' => $carta,
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/carta/{id}/documento", name="direccion_descargar_carta", methods={"GET"})
     */
    public function descargarCarta(Carta $carta, FileUploader $fileUploader): Response
    {
        $storedFilename = basename($carta->getDocumento());
        if ($storedFilename === '' || strtolower(pathinfo($storedFilename, PATHINFO_EXTENSION)) !== 'pdf') {
            throw $this->createNotFoundException('Documento no encontrado.');
        }

        $downloadName = preg_replace('/[^A-Za-z0-9._-]/', '_', $storedFilename);
        $downloadName = trim($downloadName, '._-');
        if ($downloadName === '' || strtolower(pathinfo($downloadName, PATHINFO_EXTENSION)) !== 'pdf') {
            throw $this->createNotFoundException('Documento no encontrado.');
        }

        $path = $fileUploader->getTargetDirectory() . DIRECTORY_SEPARATOR . $storedFilename;
        // Retain fallback access for legacy files while the database is unavailable for verification.
        if (!is_file($path)) {
            $path = $this->getParameter('kernel.project_dir') . '/public/uploads/cartas/' . $storedFilename;
        }
        if (!is_file($path)) {
            throw $this->createNotFoundException('Documento no encontrado.');
        }

        $response = new BinaryFileResponse($path);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $downloadName);
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }

    /**
     * @Route("/editar-contrasena/{id}/{ruta}", name="direccion_editar_contrasena", methods={"GET", "POST"})
     * @param Request $request
     * @param Usuario $usuario
     * @param string $ruta
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return Response
     */
    public function editarContrasena(Request $request, Usuario $usuario, string $ruta, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        if (!$this->isGranted("ROLE_ADMIN") && $usuario->getId() !== $this->getUser()->getId()) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }
        if ($request->isXmlHttpRequest()) {
            $form = $this->createForm(UsuarioType::class, $usuario);
            $form->remove('nombre');
            $form->remove('usuario');
            $form->remove('roles');
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $usuario = $form->getData();

                $plainPassword = $usuario->getPassword();
                $password = $userPasswordEncoder->encodePassword($usuario, $plainPassword);
                $usuario->setPassword($password);

                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha editado su contraseña');

                $this->entityManager->persist($traza);
                $this->entityManager->persist($usuario);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Contraseña editada correctamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                }

                return new ModalRedirectResponse($this->generateUrl($ruta));
            }

            return $this->render('direccion/password.html.twig', [
                'usuario' => $usuario,
                'ruta' => $ruta,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }
}
