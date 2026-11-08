<?php

namespace App\Controller\Registro;

use App\Entity\Legajo;
use App\Entity\Traza;
use App\Form\LegajoType;
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
class LegajoController extends AbstractController
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
     * @Route("/listar-legajos", name="registro_listar_legajos", methods={"GET"})
     * @return Response
     */
    public function listarLegajos(): Response
    {
        return $this->render('registro/legajo/listar.html.twig', [
            'legajos' => $this->entityManager->getRepository(Legajo::class)->findBy(array(), array('numero' => 'ASC')),
        ]);
    }

    /**
     * @Route("/agregar-legajo", name="registro_agregar_legajo", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function agregarLegajos(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $legajo = new Legajo();
            $form = $this->createForm(LegajoType::class, $legajo);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha creado un nuevo legajo con identificador LEG' . $legajo->getNumero());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($legajo);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Legajo agregado correctamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    if (\App\Service\PersistenceExceptionClassifier::isUniqueConstraintViolation($e)) {
                        $this->addFlash('error', 'Legajo no agregado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('registro_listar_legajos'));
            }

            return $this->render('registro/legajo/agregar.html.twig', [
                'legajo' => $legajo,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/editar-legajo/{id}", name="registro_editar_legajo", methods={"GET", "POST"})
     * @param Request $request
     * @param Legajo $legajo
     * @return Response
     */
    public function editarLegajos(Request $request, Legajo $legajo): Response
    {
        if ($request->isXmlHttpRequest()) {
            $oldData = clone $legajo;

            $form = $this->createForm(LegajoType::class, $legajo);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha editado el legajo con identificador LEG' . $oldData->getNumero() . '. Nuevo identicador LEG' . $legajo->getNumero());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($legajo);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Legajo editado correctamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    if (\App\Service\PersistenceExceptionClassifier::isUniqueConstraintViolation($e)) {
                        $this->addFlash('error', 'Legajo no editado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('registro_listar_legajos'));
            }

            return $this->render('registro/legajo/editar.html.twig', [
                'legajo' => $legajo,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/eliminar-legajo/{id}", name="registro_eliminar_legajo", methods={"GET", "POST"})
     * @param Request $request
     * @param Legajo $legajo
     * @return Response
     */
    public function eliminarLegajo(Request $request, Legajo $legajo): Response
    {
        if ($request->isXmlHttpRequest()) {
            $form = $this->createDeleteForm($legajo);

            if ($request->getMethod() == "POST") {
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $traza = new Traza();
                    $traza->setTipo('danger');
                    $traza->setUsuario($this->getUser()->getNombre());
                    $traza->setContenido('Ha eliminado el legajo con identificador LEG' . $legajo->getNumero());

                    $this->entityManager->persist($traza);
                    $this->entityManager->remove($legajo);

                    try {
                        $this->entityManager->flush();
                        $this->addFlash('success', 'Legajo eliminado correctamente.');
                    } catch (Exception $e) {
                        error_log((string) $e);
                        if (\App\Service\PersistenceExceptionClassifier::isForeignKeyConstraintViolation($e)) {
                            $this->addFlash('error', 'Legajo no eliminado, conserva expedientes o libros asociados.');
                        } else {
                            $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                        }
                    }

                    return new ModalRedirectResponse($this->generateUrl('registro_listar_legajos'));
                }
            }

            return $this->render('registro/legajo/eliminar.html.twig', array(
                'legajo' => $legajo,
                'form' => $form->createView()
            ));
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @param Legajo $legajo
     * @return FormInterface
     */
    private function createDeleteForm(Legajo $legajo): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('registro_eliminar_legajo', array('id' => $legajo->getId())))
            ->setMethod('POST')
            ->getForm();
    }
}
