<?php

namespace App\Controller;

use App\Entity\Traza;
use App\Entity\Usuario;
use App\Form\UsuarioType;
use App\Repository\TrazasRepository;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
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
     * @Route("/listar-usuarios", name="admin_listar_usuarios", methods={"GET"})
     * @param UsuarioRepository $usuarioRepository
     * @return Response
     */
    public function listarUsuarios(UsuarioRepository $usuarioRepository): Response
    {
        return $this->render('admin/listar.html.twig', [
            'usuarios' => $usuarioRepository->findAll(),
        ]);
    }

    /**
     * @Route("/agregar-usuario", name="admin_agregar_usuario", methods={"GET", "POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return Response
     */
    public function agregarUsuario(Request $request, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        if ($request->isXmlHttpRequest()) {
            $usuario = new Usuario();
            $form = $this->createForm(UsuarioType::class, $usuario);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $usuario = $form->getData();

                $plainPassword = $usuario->getPassword();
                $password = $userPasswordEncoder->encodePassword($usuario, $plainPassword);
                $usuario->setPassword($password);

                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha creado el usuario ' . $usuario->getNombre());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($usuario);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Usuario agregado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1062) {
                        $this->addFlash('error', 'Usuario no agregado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'Error : ' . $e->getErrorCode() . '. Consulte al grupo de desarrollo.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('admin_listar_usuarios'));
            }

            return $this->render('admin/agregar.html.twig', [
                'usuario' => $usuario,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/editar-perfil-usuario/{id}", name="admin_editar_perfil_usuario", methods={"GET", "POST"})
     * @param Request $request
     * @param Usuario $usuario
     * @return Response
     */
    public function editarPerfil(Request $request, Usuario $usuario): Response
    {
        if ($request->isXmlHttpRequest()) {
            $form = $this->createForm(UsuarioType::class, $usuario);
            $form->remove('password');
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha editado el perfil del usuario ' . $usuario->getNombre());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($usuario);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Usuario editado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1062) {
                        $this->addFlash('error', 'Usuario no editado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'Error : ' . $e->getErrorCode() . '. Consulte al grupo de desarrollo.');
                    }
                }
                return new ModalRedirectResponse($this->generateUrl('admin_listar_usuarios'));
            }

            return $this->render('admin/perfil.html.twig', [
                'usuario' => $usuario,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/editar-contrasena-usuario/{id}", name="admin_editar_contrasena_usuario", methods={"GET", "POST"})
     * @param Request $request
     * @param Usuario $usuario
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return Response
     */
    public function editarContrasena(Request $request, Usuario $usuario, UserPasswordEncoderInterface $userPasswordEncoder): Response
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
                $traza->setContenido('Ha editado la contraseña del usuario ' . $usuario->getNombre());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($usuario);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Contraseña editada correctamente.');
                } catch (Exception $e) {
                    $this->addFlash('error', 'Error : ' . $e->getErrorCode() . '. Consulte al grupo de desarrollo.');
                }

                return new ModalRedirectResponse($this->generateUrl('admin_listar_usuarios'));
            }

            return $this->render('admin/password.html.twig', [
                'usuario' => $usuario,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/eliminar-usuario/{id}", name="admin_eliminar_usuario", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Usuario $usuario
     * @return Response
     */
    public function eliminarUsuario(Request $request, Usuario $usuario): Response
    {
        if ($request->isXmlHttpRequest()) {
            $form = $this->createDeleteForm($usuario);

            if ($request->getMethod() == "DELETE") {
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $traza = new Traza();
                    $traza->setTipo('danger');
                    $traza->setUsuario($this->getUser()->getNombre());
                    $traza->setContenido('Ha eliminado el usuario ' . $usuario->getNombre());

                    $this->entityManager->persist($traza);
                    $this->entityManager->remove($usuario);

                    try {
                        $this->entityManager->flush();
                        $this->addFlash('success', 'Usuario eliminado correctamente.');
                    } catch (Exception $e) {
                        $this->addFlash('error', 'Error : ' . $e->getErrorCode() . '. Consulte al grupo de desarrollo.');
                    }

                    return new ModalRedirectResponse($this->generateUrl('admin_listar_usuarios'));
                }
            }

            return $this->render('admin/eliminar.html.twig', array(
                'usuario' => $usuario,
                'form' => $form->createView()
            ));
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @param Usuario $usuario
     * @return FormInterface
     */
    private function createDeleteForm(Usuario $usuario): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_eliminar_usuario', array('id' => $usuario->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Route("/listar-trazas", name="admin_listar_trazas")
     * @param TrazasRepository $trazasRepository
     * @return Response
     */
    public function listarTrazas(TrazasRepository $trazasRepository): Response
    {
        return $this->render('admin/trazas.html.twig', [
            'trazas' => $trazasRepository->obtenerTrazasOrdenadasPorFecha()
        ]);
    }
}
