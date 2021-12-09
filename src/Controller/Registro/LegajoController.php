<?php

namespace App\Controller\Registro;

use App\Entity\Legajo;
use App\Form\LegajoType;
use App\Repository\LegajoRepository;
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
class LegajoController extends AbstractController
{
    /**
     * @Route("/listar-legajos", name="listar_legajos", methods={"GET"})
     * @param LegajoRepository $legajoRepository
     * @return Response
     */
    public function listar(LegajoRepository $legajoRepository): Response
    {
        return $this->render('registro/legajos/listar.html.twig', [
            'legajos' => $legajoRepository->findAllOrderByAsc(),
        ]);
    }

    /**
     * @Route("/agregar-legajos", name="agregar_legajo", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function agregar(Request $request, EntityManagerInterface $entityManager): Response
    {
        $legajo = new Legajo();
        $form = $this->createForm(LegajoType::class, $legajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($legajo);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El legajo se ha agregado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'Legajo no agregado, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_legajos'));
        }

        return $this->render('registro/legajos/agregar.html.twig', [
            'legajos' => $legajo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editar-legajos/{id}", name="editar_legajo", methods={"GET","POST"})
     * @param Request $request
     * @param Legajo $legajo
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editar(Request $request, Legajo $legajo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LegajoType::class, $legajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', 'El legajo se ha sido editado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'Legajo no editado, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_legajos'));
        }

        return $this->render('registro/legajos/editar.html.twig', [
            'legajo' => $legajo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/eliminar-legajos/{id}", name="eliminar_legajo", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Legajo $legajo
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function eliminar(Request $request, Legajo $legajo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createDeleteForm($legajo);

        if ($request->getMethod() == "DELETE") {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->remove($legajo);

                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'El legajo se ha eliminado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1451)  {
                        $this->addFlash('error', 'Legajo no eliminado, conserva expedientes y/o libros asociados.');
                    } else
                    {
                        $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('listar_legajos'));
            }
        }

        return $this->render('registro/legajos/eliminar.html.twig', array(
            'legajo' => $legajo,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Legajo $legajo
     * @return FormInterface
     */
    private function createDeleteForm(Legajo $legajo): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eliminar_legajo', array('id' => $legajo->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
