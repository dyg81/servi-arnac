<?php

namespace App\Controller\Registro;

use App\Entity\Anaquel;
use App\Form\AnaquelType;
use App\Repository\AnaquelRepository;
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
class AnaquelController extends AbstractController
{
    /**
     * @Route("/listar-anaqueles", name="listar_anaqueles", methods={"GET"})
     * @param AnaquelRepository $anaquelRepository
     * @return Response
     */
    public function listar(AnaquelRepository $anaquelRepository): Response
    {
        return $this->render('registro/anaqueles/listar.html.twig', [
            'anaqueles' => $anaquelRepository->findAllOrderByAsc(),
        ]);
    }

    /**
     * @Route("/agregar-anaqueles", name="agregar_anaquel", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function agregar(Request $request, EntityManagerInterface $entityManager): Response
    {
        $anaquel = new Anaquel();
        $form = $this->createForm(AnaquelType::class, $anaquel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($anaquel);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El anaquel se ha agregado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'Anaquel no agregado, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_anaqueles'));
        }

        return $this->render('registro/anaqueles/agregar.html.twig', [
            'anaquel' => $anaquel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editar-anaqueles/{id}", name="editar_anaquel", methods={"GET","POST"})
     * @param Request $request
     * @param Anaquel $anaquel
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editar(Request $request, Anaquel $anaquel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnaquelType::class, $anaquel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', 'El anaquel se ha sido editado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'Anaquel no editado, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_anaqueles'));
        }

        return $this->render('registro/anaqueles/editar.html.twig', [
            'anaquel' => $anaquel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/eliminar-anaqueles/{id}", name="eliminar_anaquel", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Anaquel $anaquel
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function eliminar(Request $request, Anaquel $anaquel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createDeleteForm($anaquel);

        if ($request->getMethod() == "DELETE") {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->remove($anaquel);

                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'El anaquel se ha eliminado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1451)  {
                        $this->addFlash('error', 'Anaquel no eliminado, conserva expedientes y/o libros asociados.');
                    } else
                    {
                        $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('listar_anaqueles'));
            }
        }

        return $this->render('registro/anaqueles/eliminar.html.twig', array(
            'anaquel' => $anaquel,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Anaquel $anaquel
     * @return FormInterface
     */
    private function createDeleteForm(Anaquel $anaquel): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eliminar_anaquel', array('id' => $anaquel->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
