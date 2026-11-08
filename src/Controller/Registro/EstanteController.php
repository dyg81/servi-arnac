<?php

namespace App\Controller\Registro;

use App\Entity\Estante;
use App\Entity\Traza;
use App\Form\EstanteType;
use Doctrine\ORM\EntityManagerInterface;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/registro")
 */
class EstanteController extends AbstractController
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
     * @Route("/listar-estantes", name="registro_listar_estantes", methods={"GET"})
     * @return Response
     */
    public function listarEstantes(): Response
    {
        return $this->render('registro/estante/listar.html.twig', [
            'estantes' => $this->entityManager->getRepository(Estante::class)->findBy(array(), array('numero' => 'ASC')),
        ]);
    }

    /**
     * @Route("/agregar-estante", name="registro_agregar_estante", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function agregarEstente(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $estante = new Estante();
            $form = $this->createForm(EstanteType::class, $estante);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha creado un nuevo estante con identificador EST' . $estante->getNumero());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($estante);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Estante agregado correctamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    if (\App\Service\PersistenceExceptionClassifier::isUniqueConstraintViolation($e)) {
                        $this->addFlash('error', 'Estante no agregado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('registro_listar_estantes'));
            }

            return $this->render('registro/estante/agregar.html.twig', [
                'estante' => $estante,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/editar-estante/{id}", name="registro_editar_estante", methods={"GET", "POST"})
     * @param Request $request
     * @param Estante $estante
     * @return Response
     */
    public function editarEstante(Request $request, Estante $estante): Response
    {
        if ($request->isXmlHttpRequest()) {
            $oldData = clone $estante;

            $form = $this->createForm(EstanteType::class, $estante);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha editado el estante con identificador EST' . $oldData->getNumero() . '. Nuevo identificador EST' . $estante->getNumero());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($estante);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Estante editado correctamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    if (\App\Service\PersistenceExceptionClassifier::isUniqueConstraintViolation($e)) {
                        $this->addFlash('error', 'Estante no editado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('registro_listar_estantes'));
            }

            return $this->render('registro/estante/editar.html.twig', [
                'estante' => $estante,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }

    }

    /**
     * @Route("/eliminar-estante/{id}", name="registro_eliminar_estante", methods={"GET", "POST"})
     * @param Request $request
     * @param Estante $estante
     * @return Response
     */
    public function eliminarEstante(Request $request, Estante $estante): Response
    {
        if ($request->isXmlHttpRequest()) {
            $form = $this->createDeleteForm($estante);

            if ($request->getMethod() == "POST") {
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $traza = new Traza();
                    $traza->setTipo('danger');
                    $traza->setUsuario($this->getUser()->getNombre());
                    $traza->setContenido('Ha eliminado el estante con identificador EST' . $estante->getNumero());

                    $this->entityManager->persist($traza);
                    $this->entityManager->remove($estante);

                    try {
                        $this->entityManager->flush();
                        $this->addFlash('success', 'Estante eliminado correctamente.');
                    } catch (Exception $e) {
                        error_log((string) $e);
                        if (\App\Service\PersistenceExceptionClassifier::isForeignKeyConstraintViolation($e)) {
                            $this->addFlash('error', 'Estante no eliminado, conserva expedientes o libros asociados.');
                        } else {
                            $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                        }
                    }

                    return new ModalRedirectResponse($this->generateUrl('registro_listar_estantes'));
                }
            }

            return $this->render('registro/estante/eliminar.html.twig', array(
                'estante' => $estante,
                'form' => $form->createView()
            ));
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @param Estante $estante
     * @return FormInterface
     */
    private function createDeleteForm(Estante $estante): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('registro_eliminar_estante', array('id' => $estante->getId())))
            ->setMethod('POST')
            ->getForm();
    }
}
