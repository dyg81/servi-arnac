<?php

namespace App\Controller;

use App\Entity\Expediente;
use App\Entity\Libro;
use App\Entity\Reprografia;
use App\Entity\Traza;
use App\Entity\Usuario;
use App\Form\UsuarioType;
use App\Repository\ReprografiaRepository;
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
 * @Route("/reprografia")
 */
class ReprografiaController extends AbstractController
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
     * @Route("/listar-reprografias", name="servicio_listar_reprografias", methods={"GET"})
     * @param ReprografiaRepository $reprografiaRepository
     * @return Response
     */
    public function listarReprografiasNuevas(ReprografiaRepository $reprografiaRepository): Response
    {
        return $this->render('reprografia/listar.html.twig', [
            'reprografias' => $reprografiaRepository->obtenerReprografiasNoCompletadas(),
        ]);
    }

    /**
     * @Route("/listar-reprografias-completadas", name="servicio_listar_reprografias_completadas", methods={"GET"})
     * @param ReprografiaRepository $reprografiaRepository
     * @return Response
     */
    public function listarReprografiasCompletadas(ReprografiaRepository $reprografiaRepository): Response
    {
        return $this->render('reprografia/completadas.html.twig', [
            'reprografias' => $reprografiaRepository->obtenerReprografiasCompletadas(),
        ]);
    }

    /**
     * @Route("/detalles-reprografia/{id}", name="servicio_detalles_reprografia", methods={"GET"})
     * @param Request $request
     * @param Reprografia $reprografia
     * @return Response
     */
    public function verDetallesTranscripcion(Request $request, Reprografia $reprografia): Response
    {
        if ($request->isXmlHttpRequest()) {
            return $this->render('reprografia/detalles.html.twig', array(
                'reprografia' => $reprografia,
            ));
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/procesar-reprografia/{id}", name="servicio_procesar_reprografia", methods={"POST"})
     * @param Reprografia $reprografia
     * @return Response
     */
    public function procesarReprografia(Request $request, Reprografia $reprografia): Response
    {
        if (!$this->isCsrfTokenValid('servicio_procesar_reprografia', $request->request->get('_token'))) { throw $this->createAccessDeniedException('Token CSRF inválido.'); }
        if ($reprografia->getEstado() !== Reprografia::ESTADO_SOLICITADO) {
            throw $this->createAccessDeniedException('La solicitud ya no está en el estado esperado para esta acción.');
        }
        $this->assertServiceDocumentExists($reprografia->getDocumentoTipo(), $reprografia->getDocumentoId());
        $reprografia->setEstado(Reprografia::ESTADO_EN_PROCESO);
        $reprografia->setFechaActualizacion(new DateTime());

        if ($reprografia->getDocumentoTipo() === 'lib') {
            $libro = $this->entityManager->getRepository(Libro::class)->find($reprografia->getDocumentoId());
            $libro->setEstado(Libro::LIB_ESTADO_DIG_EN_PROCESO);
            $this->entityManager->persist($libro);
        } elseif ($reprografia->getDocumentoTipo() === 'exp') {
            $expediente = $this->entityManager->getRepository(Expediente::class)->find($reprografia->getDocumentoId());
            $expediente->setEstado(Expediente::EXP_ESTADO_DIG_EN_PROCESO);
            $this->entityManager->persist($expediente);
        }

        $traza = new Traza();

        $traza->setTipo('warning');
        $traza->setUsuario($this->getUser()->getNombre());
        $traza->setContenido('Ha comenzado una reprografía asociada al cliente ' . $reprografia->getCliente());

        $this->entityManager->persist($traza);
        $this->entityManager->persist($reprografia);

        try {
            $this->entityManager->flush();
            $this->addFlash('success', 'Reprografía actualizada correctamente.');
        } catch (Exception $e) {
            error_log((string) $e);
            $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
        }

        return $this->redirectToRoute('servicio_listar_reprografias');
    }

    /**
     * @Route("/terminar-reprografia/{id}", name="servicio_terminar_reprografia", methods={"POST"})
     * @param Reprografia $reprografia
     * @return Response
     */
    public function terminarReprografia(Request $request, Reprografia $reprografia): Response
    {
        if (!$this->isCsrfTokenValid('servicio_terminar_reprografia', $request->request->get('_token'))) { throw $this->createAccessDeniedException('Token CSRF inválido.'); }
        if ($reprografia->getEstado() !== Reprografia::ESTADO_EN_PROCESO) {
            throw $this->createAccessDeniedException('La solicitud ya no está en el estado esperado para esta acción.');
        }
        $this->assertServiceDocumentExists($reprografia->getDocumentoTipo(), $reprografia->getDocumentoId());
        $reprografia->setEstado(Reprografia::ESTADO_TERMINADO);
        $reprografia->setFechaActualizacion(new DateTime());

        if ($reprografia->getDocumentoTipo() === 'lib') {
            $libro = $this->entityManager->getRepository(Libro::class)->find($reprografia->getDocumentoId());
            $libro->setEstado(Libro::LIB_ESTADO_DIG_TERMINADA);
            $this->entityManager->persist($libro);
        } elseif ($reprografia->getDocumentoTipo() === 'exp') {
            $expediente = $this->entityManager->getRepository(Expediente::class)->find($reprografia->getDocumentoId());
            $expediente->setEstado(Expediente::EXP_ESTADO_DIG_TERMINADA);
            $this->entityManager->persist($expediente);
        }

        $traza = new Traza();

        $traza->setTipo('warning');
        $traza->setUsuario($this->getUser()->getNombre());
        $traza->setContenido('Ha terminado una reprografía asociada al cliente ' . $reprografia->getCliente());

        $this->entityManager->persist($traza);
        $this->entityManager->persist($reprografia);

        try {
            $this->entityManager->flush();
            $this->addFlash('success', 'Reprografía concluida correctamente.');
        } catch (Exception $e) {
            error_log((string) $e);
            $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
        }

        return $this->redirectToRoute('servicio_listar_reprografias');
    }

    /**
     * @Route("/editar-contrasena/{id}/{ruta}", name="reprografia_editar_contrasena", methods={"GET", "POST"})
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

            return $this->render('reprografia/password.html.twig', [
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
