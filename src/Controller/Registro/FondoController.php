<?php

namespace App\Controller\Registro;

use App\Entity\Expediente;
use App\Entity\Fondo;
use App\Entity\Libro;
use App\Entity\Traza;
use App\Form\FondoType;
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
class FondoController extends AbstractController
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
     * @Route("/listar-fondos", name="registro_listar_fondos", methods={"GET"})
     * @return Response
     */
    public function listarFondos(): Response
    {
        return $this->render('registro/fondo/listar.html.twig', [
            'fondos' => $this->entityManager->getRepository(Fondo::class)->findAll(),
        ]);
    }

    /**
     * @Route("/agregar/fondo", name="registro_agregar_fondo", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function agregarFondo(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $fondo = new Fondo();
            $form = $this->createForm(FondoType::class, $fondo);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha creado el fondo ' . $fondo->getNombre());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($fondo);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Fondo agregado correctamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    if (\App\Service\PersistenceExceptionClassifier::isUniqueConstraintViolation($e)) {
                        $this->addFlash('error', 'Fondo no agregado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('registro_listar_fondos'));
            }

            return $this->render('registro/fondo/agregar.html.twig', [
                'fondo' => $fondo,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/editar-fondo/{id}", name="registro_editar_fondo", methods={"GET", "POST"})
     * @param Request $request
     * @param Fondo $fondo
     * @return Response
     */
    public function editarFondo(Request $request, Fondo $fondo): Response
    {
        if ($request->isXmlHttpRequest()) {
            $oldData = clone $fondo;
            $form = $this->createForm(FondoType::class, $fondo);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());

                if ($oldData->getNombre() != $fondo->getNombre()) {
                    $traza->setContenido('Ha editado el nombre del fondo ' . $oldData->getNombre() . ' a ' . $fondo->getNombre());

                    $expedientes = $this->entityManager->getRepository(Expediente::class)->findAll();

                    foreach ($expedientes as $expediente) {
                        $expediente->setSlug($expediente->getNumero());
                        $this->entityManager->persist($expediente);
                    }

                    $libros = $this->entityManager->getRepository(Libro::class)->findAll();

                    foreach ($libros as $libro) {
                        $libro->setSlug($libro->getAnno() . '-' . $libro->getTomo());
                        $this->entityManager->persist($libro);
                    }
                } else {
                    $traza->setContenido('Ha editado los depósitos asociados al fondo ' . $fondo->getNombre());
                }

                $this->entityManager->persist($traza);
                $this->entityManager->persist($fondo);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Fondo editado correctamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    if (\App\Service\PersistenceExceptionClassifier::isUniqueConstraintViolation($e)) {
                        $this->addFlash('error', 'Fondo no editado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('registro_listar_fondos'));
            }

            return $this->render('registro/fondo/editar.html.twig', [
                'fondo' => $fondo,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/eliminar-fondo/{id}", name="registro_eliminar_fondo", methods={"GET", "POST"})
     * @param Request $request
     * @param Fondo $fondo
     * @return Response
     */
    public function delete(Request $request, Fondo $fondo): Response
    {
        if ($request->isXmlHttpRequest()) {
            $form = $this->createDeleteForm($fondo);

            if ($request->getMethod() == "POST") {
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $traza = new Traza();
                    $traza->setTipo('danger');
                    $traza->setUsuario($this->getUser()->getNombre());
                    $traza->setContenido('Ha eliminado el fondo ' . $fondo->getNombre());

                    $this->entityManager->persist($traza);
                    $this->entityManager->remove($fondo);

                    try {
                        $this->entityManager->flush();
                        $this->addFlash('success', 'Fondo eliminado correctamente.');
                    } catch (Exception $e) {
                        error_log((string) $e);
                        if (\App\Service\PersistenceExceptionClassifier::isForeignKeyConstraintViolation($e)) {
                            $this->addFlash('error', 'Fondo no eliminado, conserva expedientes o libros asociados.');
                        } else {
                            $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                        }
                    }

                    return new ModalRedirectResponse($this->generateUrl('registro_listar_fondos'));
                }
            }

            return $this->render('registro/fondo/eliminar.html.twig', array(
                'fondo' => $fondo,
                'form' => $form->createView()
            ));
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @param Fondo $fondo
     * @return FormInterface
     */
    private function createDeleteForm(Fondo $fondo): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('registro_eliminar_fondo', array('id' => $fondo->getId())))
            ->setMethod('POST')
            ->getForm();
    }
}
