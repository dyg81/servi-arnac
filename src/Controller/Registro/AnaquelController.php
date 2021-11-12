<?php

namespace App\Controller\Registro;

use App\Entity\Anaquel;
use App\Form\AnaquelType;
use App\Repository\AnaquelRepository;
use Doctrine\DBAL\Exception;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/anaqueles")
 */
class AnaquelController extends AbstractController
{
    /**
     * @Route("/listar", name="listar_anaqueles", methods={"GET"})
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
     * @Route("/agregar", name="agregar_anaquel", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function agregar(Request $request): Response
    {
        $anaquel = new Anaquel();
        $form = $this->createForm(AnaquelType::class, $anaquel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($anaquel);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El anaquel '.$anaquel->getNumero().' ha sido creado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'El anaquel '.$anaquel->getNumero().' no se pudo agregar, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error desconocido: '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
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
     * @Route("/editar/{id}", name="editar_anaquel", methods={"GET","POST"})
     * @param Request $request
     * @param Anaquel $anaquel
     * @return Response
     */
    public function editar(Request $request, Anaquel $anaquel): Response
    {
        $form = $this->createForm(AnaquelType::class, $anaquel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El anaquel '.$anaquel->getNumero().' ha sido editado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'El anaquel no se pudo editar, ya existe uno con igual identificador.');
                } else
                {
                    $this->addFlash('error', 'Error desconocido: '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
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
     * @Route("/eliminar/{id}", name="eliminar_anaquel", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Anaquel $anaquel
     * @return Response
     */
    public function eliminar(Request $request, Anaquel $anaquel): Response
    {
        $form = $this->createDeleteForm($anaquel);

        if ($request->getMethod() == "DELETE") {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($anaquel);

                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'El anaquel '.$anaquel->getNumero().' ha sido eliminado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1451)  {
                        $this->addFlash('error', 'El anaquel no se pudo eliminar, conserva expedientes o libros asociados.');
                    } else
                    {
                        $this->addFlash('error', 'Error desconocido: '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
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
    private function createDeleteForm(Anaquel $anaquel)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eliminar_anaquel', array('id' => $anaquel->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
