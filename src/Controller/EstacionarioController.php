<?php

namespace App\Controller;

use App\Entity\Expediente;
use App\Entity\Investigacion;
use App\Entity\Libro;
use App\Entity\Traza;
use App\Entity\Usuario;
use App\Form\UsuarioType;
use App\Repository\ExpedienteRepository;
use App\Repository\InvestigacionRepository;
use App\Repository\LibroRepository;
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
 * @Route("/estacionario")
 */
class EstacionarioController extends AbstractController
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
     * @Route("/listar-investigaciones", name="estacionario_listar_investigaciones_nuevas")
     * @param InvestigacionRepository $investigacionRepository
     * @return Response
     */
    public function listarInvestigaciones(InvestigacionRepository $investigacionRepository): Response
    {
        return $this->render('estacionario/listar.html.twig', [
            'investigaciones' => $investigacionRepository->obtenerInvestigacionesSolicitadas(),
        ]);
    }

    /**
     * @Route("/listar-investigaciones-procesadas", name="estacionario_listar_investigaciones_procesadas", methods={"GET"})
     * @param InvestigacionRepository $investigacionRepository
     * @return Response
     */
    public function listarInvestigacionesProcesadas(InvestigacionRepository $investigacionRepository): Response
    {
        return $this->render('estacionario/procesadas.html.twig', array(
            'investigaciones' => $investigacionRepository->obtenerInvestigacionesProcesadas(),
        ));
    }

    /**
     * @Route("/detalles-investigacion/{id}", name="estacionario_detalles_investigacion", methods={"GET"})
     * @param Request $request
     * @param Investigacion $investigacion
     * @return Response
     */
    public function verDetallesInvestigaciones(Request $request, Investigacion $investigacion): Response
    {
        if ($request->isXmlHttpRequest()) {
            return $this->render('estacionario/detalles.html.twig', array(
                'investigacion' => $investigacion,
            ));
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/procesar-investigacion/{id}", name="estacionario_procesar_investigacion", methods={"GET"})
     * @param Investigacion $investigacion
     * @return Response
     */
    public function procesarSolicitudInvestigacion(Investigacion $investigacion): Response
    {
        $investigacion->setEstado(Investigacion::ESTADO_PROCESADO);
        $investigacion->setFechaActualizacion(new DateTime());

        foreach ($investigacion->getExpedientes() as $exp) {
            $expediente = $this->entityManager->getRepository(Expediente::class)->find($exp);
            if (($expediente->getEstado() == Expediente::EXP_ESTADO_ARCHIVADO_EN_DEPOSITO) or ($expediente->getEstado() == Expediente::EXP_ESTADO_EN_SALA_RECEPCION)) {
                $expediente->setEstado(Expediente::EXP_ESTADO_SOLICITADO_PARA_SALA);
                $this->entityManager->persist($expediente);
            } else {
                $investigacion->removeExpediente($expediente);
            }
        }

        foreach ($investigacion->getLibros() as $lib) {
            $libro = $this->entityManager->getRepository(Libro::class)->find($lib);
            if (($libro->getEstado() == Libro::LIB_ESTADO_ARCHIVADO_EN_DEPOSITO) or ($libro->getEstado() == Libro::LIB_ESTADO_EN_SALA_RECEPCION)) {
                $libro->setEstado(Libro::LIB_ESTADO_SOLICITADO_PARA_SALA);
                $this->entityManager->persist($libro);
            } else {
                $investigacion->removeLibro($libro);
            }
        }

        $traza = new Traza();
        $traza->setTipo('warning');
        $traza->setUsuario($this->getUser()->getNombre());
        $traza->setContenido('Ha procesado la investigación asociada al cliente ' . $investigacion->getCliente());

        $this->entityManager->persist($traza);
        $this->entityManager->persist($investigacion);

        try {
            $this->entityManager->flush();
            $this->addFlash('success', 'Investigación procesada correctamente.');
        } catch (Exception $e) {
            $this->addFlash('error', 'Error : ' . $e->getErrorCode() . '. Consulte al grupo de desarrollo.');
        }

        return $this->redirectToRoute('estacionario_listar_investigaciones_nuevas');
    }

    /**
     * @Route("/cancelar-investigacion/{id}", name="estacionario_cancelar_investigacion", methods={"GET"})
     * @param Investigacion $investigacion
     * @return Response
     */
    public function cancelarSolicitudInvestigacion(Investigacion $investigacion): Response
    {
        $investigacion->setEstado(Investigacion::ESTADO_CANCELADO);
        $investigacion->setFechaActualizacion(new DateTime());

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
            $this->addFlash('error', 'Error : ' . $e->getErrorCode() . '. Consulte al grupo de desarrollo.');
        }

        return $this->redirectToRoute('estacionario_listar_investigaciones_nuevas');
    }

    /**
     * @Route("/listar-documentos-por-archivar", name="estacionario_listar_documentos_por_archivar", methods={"GET"})
     * @param ExpedienteRepository $expedienteRepository
     * @param LibroRepository $libroRepository
     * @return Response
     */
    public function listarExpedientesPorArchivar(ExpedienteRepository $expedienteRepository, LibroRepository $libroRepository): Response
    {
        return $this->render('estacionario/expedientes.html.twig', array(
            'expedientes' => $expedienteRepository->obtenerExpedientesPorArchivar(),
            'libros' => $libroRepository->obtenerLibrosPorArchivar(),
        ));
    }

    /**
     * @Route("/archivar-documentos", name="estacionario_archivar_documentos", methods={"GET"})
     * @param ExpedienteRepository $expedienteRepository
     * @param LibroRepository $libroRepository
     * @return Response
     */
    public function archivarDocumentos(ExpedienteRepository $expedienteRepository, LibroRepository $libroRepository): Response
    {
        $expedientes = $expedienteRepository->obtenerExpedientesPorArchivar();
        $libros = $libroRepository->obtenerLibrosPorArchivar();

        if (!$expedientes and !$libros) {
            $this->addFlash('info', 'No hay documentos por archivar.');

            return $this->redirectToRoute('estacionario_listar_documentos_por_archivar');
        }

        $traza = new Traza();
        $traza->setTipo('warning');
        $traza->setUsuario($this->getUser()->getNombre());

        $texto = 'Ha archivado los documentos ';

        foreach ($expedientes as $expediente) {
            $expediente->setEstado(Expediente::EXP_ESTADO_ARCHIVADO_EN_DEPOSITO);
            $this->entityManager->persist($expediente);

            $texto = $texto . $expediente . ' ';
        }

        foreach ($libros as $libro) {
            $libro->setEstado(Libro::LIB_ESTADO_ARCHIVADO_EN_DEPOSITO);
            $this->entityManager->persist($libro);

            $texto = $texto . $libro . ' ';
        }

        $traza->setContenido($texto);
        $this->entityManager->persist($traza);

        try {
            $this->entityManager->flush();
            $this->addFlash('success', 'Documentos archivados correctamente.');
        } catch (Exception $e) {
            $this->addFlash('error', 'Error : ' . $e->getErrorCode() . '. Consulte al grupo de desarrollo.');
        }

        return $this->redirectToRoute('estacionario_listar_documentos_por_archivar');
    }

    /**
     * @Route("/archivar-documento/{documento}/{tipo}", name="estacionario_archivar_documento", methods={"GET"})
     * @param ExpedienteRepository $expedienteRepository
     * @param LibroRepository $libroRepository
     * @param int $documento
     * @param string $tipo
     * @return Response
     */
    public function archivarDocumento(ExpedienteRepository $expedienteRepository, LibroRepository $libroRepository, int $documento, string $tipo): Response
    {
        $traza = new Traza();
        $traza->setTipo('warning');
        $traza->setUsuario($this->getUser()->getNombre());

        if ($tipo == 'exp') {
            $expediente = $expedienteRepository->find($documento);
            $expediente->setEstado(Expediente::EXP_ESTADO_ARCHIVADO_EN_DEPOSITO);
            $this->entityManager->persist($expediente);

            $traza->setContenido('Ha archivado el documento ' . $expediente);
        } elseif ($tipo == 'lib') {
            $libro = $libroRepository->find($documento);
            $libro->setEstado(Libro::LIB_ESTADO_ARCHIVADO_EN_DEPOSITO);
            $this->entityManager->persist($libro);

            $traza->setContenido('Ha archivado el documento ' . $libro);
        }

        $this->entityManager->persist($traza);

        try {
            $this->entityManager->flush();
            $this->addFlash('success', 'Documento archivado correctamente.');
        } catch (Exception $e) {
            $this->addFlash('error', 'Error : ' . $e->getErrorCode() . '. Consulte al grupo de desarrollo.');
        }

        return $this->redirectToRoute('estacionario_listar_documentos_por_archivar');
    }

    /**
     * @Route("/editar-contrasena/{id}/{ruta}", name="estacionario_editar_contrasena", methods={"GET", "POST"})
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

            return $this->render('estacionario/password.html.twig', [
                'usuario' => $usuario,
                'ruta' => $ruta,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }
}
