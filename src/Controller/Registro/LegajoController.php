<?php

namespace App\Controller\Registro;

use App\Entity\Legajo;
use App\Form\LegajoType;
use App\Repository\LegajoRepository;
use Doctrine\DBAL\Exception;
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
     * @return Response
     */
    public function agregar(Request $request): Response
    {
        $legajo = new Legajo();
        $form = $this->createForm(LegajoType::class, $legajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($legajo);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El legajo '.$legajo->getLegajo().' ha sido creado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'El legajo '.$legajo->getLegajo().' no se pudo agregar, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error desconocido: '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
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
     * @return Response
     */
    public function editar(Request $request, Legajo $legajo): Response
    {
        $form = $this->createForm(LegajoType::class, $legajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El legajo '.$legajo->getLegajo().' ha sido editado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'El legajo no se pudo editar, ya existe uno con igual identificador.');
                } else
                {
                    $this->addFlash('error', 'Error desconocido: '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
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
     * @return Response
     */
    public function eliminar(Request $request, Legajo $legajo): Response
    {
        $form = $this->createDeleteForm($legajo);

        if ($request->getMethod() == "DELETE") {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($legajo);

                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'El legajo '.$legajo->getLegajo().' ha sido eliminado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1451)  {
                        $this->addFlash('error', 'El legajo no se pudo eliminar, conserva expedientes o libros asociados.');
                    } else
                    {
                        $this->addFlash('error', 'Error desconocido: '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
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
    private function createDeleteForm(Legajo $legajo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eliminar_legajo', array('id' => $legajo->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
