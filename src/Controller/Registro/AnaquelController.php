<?php

namespace App\Controller\Registro;

use App\Entity\Anaquel;
use App\Entity\Traza;
use App\Form\AnaquelType;
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
class AnaquelController extends AbstractController
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
     * @Route("/listar-anaqueles", name="registro_listar_anaqueles", methods={"GET"})
     * @return Response
     */
    public function listarAnaqueles(): Response
    {
        return $this->render('registro/anaquel/listar.html.twig', [
            'anaqueles' => $this->entityManager->getRepository(Anaquel::class)->findBy(array(), array('numero' => 'ASC')),
        ]);
    }

    /**
     * @Route("/agregar-anaquel", name="registro_agregar_anaquel", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function agregarAnaquel(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $anaquel = new Anaquel();
            $form = $this->createForm(AnaquelType::class, $anaquel);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha creado un nuevo anaquel con identificador ANA' . $anaquel->getNumero());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($anaquel);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Anaquel agregado correctamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    if (\App\Service\PersistenceExceptionClassifier::isUniqueConstraintViolation($e)) {
                        $this->addFlash('error', 'Anaquel no agregado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('registro_listar_anaqueles'));
            }

            return $this->render('registro/anaquel/agregar.html.twig', [
                'anaquel' => $anaquel,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }

    }

    /**
     * @Route("/editar-anaquel/{id}", name="registro_editar_anaquel", methods={"GET", "POST"})
     * @param Request $request
     * @param Anaquel $anaquel
     * @return Response
     */
    public function editarAnaquel(Request $request, Anaquel $anaquel): Response
    {
        if ($request->isXmlHttpRequest()) {
            $oldData = clone $anaquel;
            $form = $this->createForm(AnaquelType::class, $anaquel);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha editado el anaquel con identificador ANA' . $oldData->getNumero() . '. Nuevo identificador ANA' . $anaquel->getNumero());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($anaquel);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Anaquel editado correctamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    if (\App\Service\PersistenceExceptionClassifier::isUniqueConstraintViolation($e)) {
                        $this->addFlash('error', 'Anaquel no editado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('registro_listar_anaqueles'));
            }

            return $this->render('registro/anaquel/editar.html.twig', [
                'anaquel' => $anaquel,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/eliminar-anaquel/{id}", name="registro_eliminar_anaquel", methods={"GET", "POST"})
     * @param Request $request
     * @param Anaquel $anaquel
     * @return Response
     */
    public function eliminarAnaquel(Request $request, Anaquel $anaquel): Response
    {
        if ($request->isXmlHttpRequest()) {
            $form = $this->createDeleteForm($anaquel);

            if ($request->getMethod() == "POST") {
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $traza = new Traza();
                    $traza->setTipo('danger');
                    $traza->setUsuario($this->getUser()->getNombre());
                    $traza->setContenido('Ha eliminado el anaquel con identificador ANA' . $anaquel->getNumero());

                    $this->entityManager->persist($traza);
                    $this->entityManager->remove($anaquel);

                    try {
                        $this->entityManager->flush();
                        $this->addFlash('success', 'Anaquel eliminado correctamente.');
                    } catch (Exception $e) {
                        error_log((string) $e);
                        if (\App\Service\PersistenceExceptionClassifier::isForeignKeyConstraintViolation($e)) {
                            $this->addFlash('error', 'Anaquel no eliminado, conserva expedientes o libros asociados.');
                        } else {
                            $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                        }
                    }

                    return new ModalRedirectResponse($this->generateUrl('registro_listar_anaqueles'));
                }
            }

            return $this->render('registro/anaquel/eliminar.html.twig', array(
                'anaquel' => $anaquel,
                'form' => $form->createView()
            ));
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @param Anaquel $anaquel
     * @return FormInterface
     */
    private function createDeleteForm(Anaquel $anaquel): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('registro_eliminar_anaquel', array('id' => $anaquel->getId())))
            ->setMethod('POST')
            ->getForm();
    }
}
