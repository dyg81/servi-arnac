<?php

namespace App\Controller\Sala;

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
use App\Form\UsuarioType;
use App\Repository\ExpedienteRepository;
use App\Repository\FondoRepository;
use App\Repository\LibroRepository;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/sala")
 */
class GestionController extends AbstractController
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
     * @Route("/listar-estadisticas", name="sala_listar_estadisticas")
     * @return Response
     */
    public function listarEstadisticas(): Response
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
                'investigaciones' => count($this->entityManager->getRepository(Investigacion::class)->obtenerInvestigacionesPorMes($date)),
                'transcripciones' => count($this->entityManager->getRepository(Transcripcion::class)->obtenerTranscripcionesPorMes($date)),
                'reprografias' => count($this->entityManager->getRepository(Reprografia::class)->obtenerReprografiasPorMes($date)),
                'certificaciones' => count($this->entityManager->getRepository(Certificacion::class)->obtenerCertificacionesPorMes($date)),
            );
        }

        return $this->render('sala/listar-estadisticas.html.twig', [
            'fondos_totales' => $this->entityManager->getRepository(Fondo::class)->findAll(),
            'expedientes_totales' => $this->entityManager->getRepository(Expediente::class)->findAll(),
            'libros_totales' => $this->entityManager->getRepository(Libro::class)->findAll(),
            'clientes_totales' => $this->entityManager->getRepository(Cliente::class)->findAll(),
            'investigaciones_totales' => $this->entityManager->getRepository(Investigacion::class)->findAll(),
            'transcripciones_totales' => $this->entityManager->getRepository(Transcripcion::class)->findAll(),
            'reprografias_totales' => $this->entityManager->getRepository(Reprografia::class)->findAll(),
            'certificaciones_totales' => $this->entityManager->getRepository(Certificacion::class)->findAll(),
            'fondos_mas_usados' => $this->entityManager->getRepository(Fondo::class)->obtenerFondosMasUsados(),
            'investigaciones_del_dia' => $this->entityManager->getRepository(Investigacion::class)->obtenerInvestigacionesDelDia(),
            'transcripciones_del_dia' => $this->entityManager->getRepository(Transcripcion::class)->obtenerTranscripcionesDelDia(),
            'reprografias_del_dia' => $this->entityManager->getRepository(Reprografia::class)->obtenerReprografiasDelDia(),
            'certificaciones_del_dia' => $this->entityManager->getRepository(Certificacion::class)->obtenerCertificacionesDelDia(),
            'expedientes_en_vitrina' => $this->entityManager->getRepository(Expediente::class)->obtenerExpedientesEnVitrina(),
            'libros_en_vitrina' => $this->entityManager->getRepository(Libro::class)->obtenerLibrosEnVitrina(),
            'data_anual' => $data_anual,
        ]);
    }

    /**
     * @Route("/listar-expedientes", name="sala_listar_expedientes", methods={"GET"})
     * @param ExpedienteRepository $expedienteRepository
     * @return Response
     */
    public function listarExpedientesSala(ExpedienteRepository $expedienteRepository): Response
    {
        return $this->render('sala/listar-expedientes.html.twig', [
            'expedientes' => $expedienteRepository->findAll(),
        ]);
    }

    /**
     * @Route("/listar-fondos", name="sala_listar_fondos", methods={"GET"})
     * @param FondoRepository $fondoRepository
     * @return Response
     */
    public function listarFondosSala(FondoRepository $fondoRepository): Response
    {
        return $this->render('sala/listar-fondos.html.twig', [
            'fondos' => $fondoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/listar-libros", name="sala_listar_libros", methods={"GET"})
     * @param LibroRepository $libroRepository
     * @return Response
     */
    public function listarLibrosSala(LibroRepository $libroRepository): Response
    {
        return $this->render('sala/listar-libros.html.twig', [
            'libros' => $libroRepository->findAll(),
        ]);
    }

    /**
     * @Route("/editar-contrasena/{id}/{ruta}", name="sala_editar_contrasena", methods={"GET", "POST"})
     * @param Request $request
     * @param Usuario $usuario
     * @param string $ruta
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return Response
     */
    public function editarContrasena(Request $request, Usuario $usuario, string $ruta, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
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
                    $this->addFlash('error', 'Error : ' . $e->getErrorCode() . '. Consulte al grupo de desarrollo.');
                }

                return new ModalRedirectResponse($this->generateUrl($ruta));
            }

            return $this->render('sala/password.html.twig', [
                'usuario' => $usuario,
                'ruta' => $ruta,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }
}