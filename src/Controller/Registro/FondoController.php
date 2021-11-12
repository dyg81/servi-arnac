<?php

namespace App\Controller\Registro;

use App\Entity\Fondo;
use App\Form\FondoType;
use App\Repository\FondoRepository;
use Doctrine\DBAL\Exception;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/fondos")
 */
class FondoController extends AbstractController
{
    /**
     * @Route("/listar", name="listar_fondos", methods={"GET"})
     * @param FondoRepository $fondoRepository
     * @return Response
     */
    public function listar(FondoRepository $fondoRepository): Response
    {
        return $this->render('registro/fondos/listar.html.twig', [
            'fondos' => $fondoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/agregar", name="agregar_fondo", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function agregar(Request $request): Response
    {
        $fondo = new Fondo();
        $form = $this->createForm(FondoType::class, $fondo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fondo);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El fondo ' . $fondo->getNombre() . ' ha sido creado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'El fondo '.$fondo->getNombre().' no se pudo agregar, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error desconocido: '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_fondos'));
        }

        return $this->render('registro/fondos/agregar.html.twig', [
            'fondo' => $fondo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editar/{id}", name="editar_fondo", methods={"GET","POST"})
     * @param Request $request
     * @param Fondo $fondo
     * @return Response
     */
    public function editar(Request $request, Fondo $fondo): Response
    {
        $form = $this->createForm(FondoType::class, $fondo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El fondo ' . $fondo->getNombre() . ' ha sido editado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'El fondo no se pudo editar, ya existe uno con igual identificador.');
                } else
                {
                    $this->addFlash('error', 'Error desconocido: '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_fondos'));
        }

        return $this->render('registro/fondos/editar.html.twig', [
            'fondo' => $fondo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/eliminar/{id}", name="eliminar_fondo", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Fondo $fondo
     * @return Response
     */
    public function eliminar(Request $request, Fondo $fondo): Response
    {
        $form = $this->createDeleteForm($fondo);

        if ($request->getMethod() == "DELETE") {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($fondo);

                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'El fondo ' . $fondo->getNombre() . ' ha sido eliminado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1451)  {
                        $this->addFlash('error', 'El fondo no se pudo eliminar, conserva expedientes o libros asociados.');
                    } else
                    {
                        $this->addFlash('error', 'Error desconocido: '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('listar_fondos'));
            }
        }

        return $this->render('registro/fondos/eliminar.html.twig', array(
            'fondo' => $fondo,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Fondo $fondo
     * @return FormInterface
     */
    private function createDeleteForm(Fondo $fondo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eliminar_fondo', array('id' => $fondo->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
