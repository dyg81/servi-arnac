<?php

namespace App\Controller;

use App\Entity\Expediente;
use App\Entity\Libro;
use App\Entity\Transcripcion;
use App\Entity\Traza;
use App\Entity\Usuario;
use App\Form\UsuarioType;
use App\Repository\TranscripcionRepository;
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
 * @Route("/transcripcion")
 */
class TranscripcionController extends AbstractController
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
     * @Route("/listar-transcripciones", name="servicio_listar_transcripciones", methods={"GET"})
     * @param TranscripcionRepository $transcripcionRepository
     * @return Response
     */
    public function listarTranscripcionesNuevas(TranscripcionRepository $transcripcionRepository): Response
    {
        return $this->render('transcripcion/listar.html.twig', [
            'transcripciones' => $transcripcionRepository->obtenerTranscripcionesNoCompletadas(),
        ]);
    }

    /**
     * @Route("/listar-transcripciones-completadas", name="servicio_listar_transcripciones_completadas", methods={"GET"})
     * @param TranscripcionRepository $transcripcionRepository
     * @return Response
     */
    public function listarTransripcionesCompletadas(TranscripcionRepository $transcripcionRepository): Response
    {
        return $this->render('transcripcion/completadas.html.twig', [
            'transcripciones' => $transcripcionRepository->obtenerTranscripcionesCompletadas(),
        ]);
    }

    /**
     * @Route("/detalles-transcripcion/{id}", name="servicio_detalles_transcripcion", methods={"GET"})
     * @param Request $request
     * @param Transcripcion $transcripcion
     * @return Response
     */
    public function verDetallesTranscripcion(Request $request, Transcripcion $transcripcion): Response
    {
        if ($request->isXmlHttpRequest()) {
            return $this->render('transcripcion/detalles.html.twig', array(
                'transcripcion' => $transcripcion,
            ));
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/procesar-transcripcion/{id}", name="servicio_procesar_transcripcion", methods={"POST"})
     * @param Transcripcion $transcripcion
     * @return Response
     */
    public function procesarTranscripcion(Request $request, Transcripcion $transcripcion): Response
    {
        if (!$this->isCsrfTokenValid('servicio_procesar_transcripcion', $request->request->get('_token'))) { throw $this->createAccessDeniedException('Token CSRF inválido.'); }
        if ($transcripcion->getEstado() !== Transcripcion::ESTADO_SOLICITADO) {
            throw $this->createAccessDeniedException('La solicitud ya no está en el estado esperado para esta acción.');
        }
        $this->assertServiceDocumentExists($transcripcion->getDocumentoTipo(), $transcripcion->getDocumentoId());
        $transcripcion->setEstado(Transcripcion::ESTADO_EN_PROCESO);
        $transcripcion->setFechaActualizacion(new DateTime());

        if ($transcripcion->getDocumentoTipo() === 'lib') {
            $libro = $this->entityManager->getRepository(Libro::class)->find($transcripcion->getDocumentoId());
            $libro->setEstado(Libro::LIB_ESTADO_TRANS_EN_PROCESO);
            $this->entityManager->persist($libro);
        } elseif ($transcripcion->getDocumentoTipo() === 'exp') {
            $expediente = $this->entityManager->getRepository(Expediente::class)->find($transcripcion->getDocumentoId());
            $expediente->setEstado(Expediente::EXP_ESTADO_TRANS_EN_PROCESO);
            $this->entityManager->persist($expediente);
        }

        $traza = new Traza();

        $traza->setTipo('warning');
        $traza->setUsuario($this->getUser()->getNombre());
        $traza->setContenido('Ha comenzado una transcripción asociada al cliente ' . $transcripcion->getCliente());

        $this->entityManager->persist($traza);
        $this->entityManager->persist($transcripcion);

        try {
            $this->entityManager->flush();
            $this->addFlash('success', 'Transcripción actualizada correctamente.');
        } catch (Exception $e) {
            error_log((string) $e);
            $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
        }

        return $this->redirectToRoute('servicio_listar_transcripciones');
    }

    /**
     * @Route("/terminar-transcripcion/{id}", name="servicio_terminar_transcripcion", methods={"POST"})
     * @param Transcripcion $transcripcion
     * @return Response
     */
    public function terminarTranscripcion(Request $request, Transcripcion $transcripcion): Response
    {
        if (!$this->isCsrfTokenValid('servicio_terminar_transcripcion', $request->request->get('_token'))) { throw $this->createAccessDeniedException('Token CSRF inválido.'); }
        if ($transcripcion->getEstado() !== Transcripcion::ESTADO_EN_PROCESO) {
            throw $this->createAccessDeniedException('La solicitud ya no está en el estado esperado para esta acción.');
        }
        $this->assertServiceDocumentExists($transcripcion->getDocumentoTipo(), $transcripcion->getDocumentoId());
        $transcripcion->setEstado(Transcripcion::ESTADO_TERMINADO);
        $transcripcion->setFechaActualizacion(new DateTime());

        if ($transcripcion->getDocumentoTipo() === 'lib') {
            $libro = $this->entityManager->getRepository(Libro::class)->find($transcripcion->getDocumentoId());
            $libro->setEstado(Libro::LIB_ESTADO_TRANS_TERMINADA);
            $this->entityManager->persist($libro);
        } elseif ($transcripcion->getDocumentoTipo() === 'exp') {
            $expediente = $this->entityManager->getRepository(Expediente::class)->find($transcripcion->getDocumentoId());
            $expediente->setEstado(Expediente::EXP_ESTADO_TRANS_TERMINADA);
            $this->entityManager->persist($expediente);
        }

        $traza = new Traza();

        $traza->setTipo('warning');
        $traza->setUsuario($this->getUser()->getNombre());
        $traza->setContenido('Ha terminado una transcripción asociada al cliente ' . $transcripcion->getCliente());

        $this->entityManager->persist($traza);
        $this->entityManager->persist($transcripcion);

        try {
            $this->entityManager->flush();
            $this->addFlash('success', 'Transcripción concluida correctamente.');
        } catch (Exception $e) {
            error_log((string) $e);
            $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
        }

        return $this->redirectToRoute('servicio_listar_transcripciones');
    }

    /**
     * @Route("/editar-contrasena/{id}/{ruta}", name="transcripcion_editar_contrasena", methods={"GET", "POST"})
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

            return $this->render('transcripcion/password.html.twig', [
                'usuario' => $usuario,
                'ruta' => $ruta,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    private function assertServiceDocumentExists(string $type, int $id): void
    {
        $class = $type === 'lib' ? Libro::class : ($type === 'exp' ? Expediente::class : null);
        if (!$class || !$this->entityManager->getRepository($class)->find($id)) {
            throw $this->createNotFoundException('Documento de servicio no encontrado.');
        }
    }
}
