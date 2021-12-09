<?php

namespace App\Controller\Registro;

use App\Entity\Estante;
use App\Form\EstanteType;
use App\Repository\EstanteRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
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
     * @Route("/listar-estantes", name="listar_estantes", methods={"GET"})
     * @param EstanteRepository $estanteRepository
     * @return Response
     */
    public function listar(EstanteRepository $estanteRepository): Response
    {
        return $this->render('registro/estantes/listar.html.twig', [
            'estantes' => $estanteRepository->findAllOrderByAsc(),
        ]);
    }

    /**
     * @Route("/agregar-estantes", name="agregar_estante", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function agregar(Request $request, EntityManagerInterface $entityManager): Response
    {
        $estante = new Estante();
        $form = $this->createForm(EstanteType::class, $estante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($estante);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El estante se ha agregado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'Estante no agregado, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_estantes'));
        }

        return $this->render('registro/estantes/agregar.html.twig', [
            'estante' => $estante,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editar-estantes/{id}", name="editar_estante", methods={"GET","POST"})
     * @param Request $request
     * @param Estante $estante
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editar(Request $request, Estante $estante, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EstanteType::class, $estante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', 'El estante se ha sido editado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'Estante no editado, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_estantes'));
        }

        return $this->render('registro/estantes/editar.html.twig', [
            'estante' => $estante,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/eliminar-estantes/{id}", name="eliminar_estante", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Estante $estante
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function eliminar(Request $request, Estante $estante, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createDeleteForm($estante);

        if ($request->getMethod() == "DELETE") {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->remove($estante);

                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'El estante se ha eliminado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1451)  {
                        $this->addFlash('error', 'Estante no eliminado, conserva expedientes y/o libros asociados.');
                    } else
                    {
                        $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('listar_estantes'));
            }
        }

        return $this->render('registro/estantes/eliminar.html.twig', array(
            'estante' => $estante,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Estante $estante
     * @return FormInterface
     */
    private function createDeleteForm(Estante $estante): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eliminar_estante', array('id' => $estante->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
