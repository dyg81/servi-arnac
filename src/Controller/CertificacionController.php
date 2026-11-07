<?php

namespace App\Controller;

use App\Entity\Certificacion;
use App\Entity\Expediente;
use App\Entity\Libro;
use App\Entity\Traza;
use App\Entity\Usuario;
use App\Form\UsuarioType;
use App\Repository\CertificacionRepository;
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
 * @Route("/certificacion")
 */
class CertificacionController extends AbstractController
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
     * @Route("/listar-certificaciones", name="servicio_listar_certificaciones", methods={"GET"})
     */
    public function listarCertifiacionesNuevas(CertificacionRepository $certificacionRepository): Response
    {
        return $this->render('certificacion/listar.html.twig', [
            'certificaciones' => $certificacionRepository->obtenerCertificacionesNoCompletadas(),
        ]);
    }

    /**
     * @Route("/listar-certificaciones-completadas", name="servicio_listar_certificaciones_completadas", methods={"GET"})
     * @param CertificacionRepository $certificacionRepository
     * @return Response
     */
    public function listarTransripcionesCompletadas(CertificacionRepository $certificacionRepository): Response
    {
        return $this->render('certificacion/completadas.html.twig', [
            'certificaciones' => $certificacionRepository->obtenerCertificacionesCompletadas(),
        ]);
    }

    /**
     * @Route("/detalles-certificacion/{id}", name="servicio_detalles_certificacion", methods={"GET"})
     * @param Request $request
     * @param Certificacion $certificacion
     * @return Response
     */
    public function verDetallesCertificacion(Request $request, Certificacion $certificacion): Response
    {
        if ($request->isXmlHttpRequest()) {
            return $this->render('certificacion/detalles.html.twig', array(
                'certificacion' => $certificacion,
            ));
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/procesar-certificacion/{id}", name="servicio_procesar_certificacion", methods={"POST"})
     * @param Certificacion $certificacion
     * @return Response
     */
    public function procesarCertificacion(Request $request, Certificacion $certificacion): Response
    {
        if (!$this->isCsrfTokenValid('servicio_procesar_certificacion', $request->request->get('_token'))) { throw $this->createAccessDeniedException('Token CSRF inválido.'); }
        if ($certificacion->getEstado() !== Certificacion::ESTADO_SOLICITADO) {
            throw $this->createAccessDeniedException('La solicitud ya no está en el estado esperado para esta acción.');
        }
        $this->assertServiceDocumentExists($certificacion->getDocumentoTipo(), $certificacion->getDocumentoId());
        $certificacion->setEstado(Certificacion::ESTADO_EN_PROCESO);
        $certificacion->setFechaActualizacion(new DateTime());

        if ($certificacion->getDocumentoTipo() === 'lib') {
            $libro = $this->entityManager->getRepository(Libro::class)->find($certificacion->getDocumentoId());
            $libro->setEstado(Libro::LIB_ESTADO_CERT_EN_PROCESO);
            $this->entityManager->persist($libro);
        } elseif ($certificacion->getDocumentoTipo() === 'exp') {
            $expediente = $this->entityManager->getRepository(Expediente::class)->find($certificacion->getDocumentoId());
            $expediente->setEstado(Expediente::EXP_ESTADO_CERT_EN_PROCESO);
            $this->entityManager->persist($expediente);
        }

        $traza = new Traza();

        $traza->setTipo('warning');
        $traza->setUsuario($this->getUser()->getNombre());
        $traza->setContenido('Ha comenzado una certificación asociada al cliente ' . $certificacion->getCliente());

        $this->entityManager->persist($traza);
        $this->entityManager->persist($certificacion);

        try {
            $this->entityManager->flush();
            $this->addFlash('success', 'Certificación actualizada correctamente.');
        } catch (Exception $e) {
            error_log((string) $e);
            $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
        }

        return $this->redirectToRoute('servicio_listar_certificaciones');
    }

    /**
     * @Route("/terminar-certificacion/{id}", name="servicio_terminar_certificacion", methods={"POST"})
     * @param Certificacion $certificacion
     * @return Response
     */
    public function terminarCertificacion(Request $request, Certificacion $certificacion): Response
    {
        if (!$this->isCsrfTokenValid('servicio_terminar_certificacion', $request->request->get('_token'))) { throw $this->createAccessDeniedException('Token CSRF inválido.'); }
        if ($certificacion->getEstado() !== Certificacion::ESTADO_EN_PROCESO) {
            throw $this->createAccessDeniedException('La solicitud ya no está en el estado esperado para esta acción.');
        }
        $this->assertServiceDocumentExists($certificacion->getDocumentoTipo(), $certificacion->getDocumentoId());
        $certificacion->setEstado(Certificacion::ESTADO_TERMINADO);
        $certificacion->setFechaActualizacion(new DateTime());

        if ($certificacion->getDocumentoTipo() === 'lib') {
            $libro = $this->entityManager->getRepository(Libro::class)->find($certificacion->getDocumentoId());
            $libro->setEstado(Libro::LIB_ESTADO_CERT_TERMINADA);
            $this->entityManager->persist($libro);
        } elseif ($certificacion->getDocumentoTipo() === 'exp') {
            $expediente = $this->entityManager->getRepository(Expediente::class)->find($certificacion->getDocumentoId());
            $expediente->setEstado(Expediente::EXP_ESTADO_CERT_TERMINADA);
            $this->entityManager->persist($expediente);
        }

        $traza = new Traza();

        $traza->setTipo('warning');
        $traza->setUsuario($this->getUser()->getNombre());
        $traza->setContenido('Ha terminado una certificación asociada al cliente ' . $certificacion->getCliente());

        $this->entityManager->persist($traza);
        $this->entityManager->persist($certificacion);

        try {
            $this->entityManager->flush();
            $this->addFlash('success', 'Certificación concluida correctamente.');
        } catch (Exception $e) {
            error_log((string) $e);
            $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
        }

        return $this->redirectToRoute('servicio_listar_certificaciones');
    }

    /**
     * @Route("/editar-contrasena/{id}/{ruta}", name="certificacion_editar_contrasena", methods={"GET", "POST"})
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

            return $this->render('certificacion/password.html.twig', [
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
