<?php

namespace App\Controller\Registro;

use App\Entity\Fondo;
use App\Form\FondoType;
use App\Repository\FondoRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/registro")
 */
class FondoController extends AbstractController
{
    /**
     * @Route("/listar-fondos", name="listar_fondos", methods={"GET"})
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
     * @Route("/agregar-fondos", name="agregar_fondo", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function agregar(Request $request, EntityManagerInterface $entityManager): Response
    {
        $fondo = new Fondo();
        $form = $this->createForm(FondoType::class, $fondo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($fondo);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El fondo se ha agregado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'Fondo no agregado, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
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
     * @Route("/editar-fondos/{id}", name="editar_fondo", methods={"GET","POST"})
     * @param Request $request
     * @param Fondo $fondo
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editar(Request $request, Fondo $fondo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FondoType::class, $fondo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', 'El fondo se ha sido editado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'Fondo no editado, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
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
     * @Route("/eliminar-fondos/{id}", name="eliminar_fondo", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Fondo $fondo
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function eliminar(Request $request, Fondo $fondo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createDeleteForm($fondo);

        if ($request->getMethod() == "DELETE") {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->remove($fondo);

                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'El fondo se ha eliminado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1451)  {
                        $this->addFlash('error', 'Fondo no eliminado, conserva expedientes y/o libros asociados.');
                    } else
                    {
                        $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
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
    private function createDeleteForm(Fondo $fondo): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eliminar_fondo', array('id' => $fondo->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
