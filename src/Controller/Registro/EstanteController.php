<?php

namespace App\Controller\Registro;

use App\Entity\Estante;
use App\Form\EstanteType;
use App\Repository\EstanteRepository;
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
     * @return Response
     */
    public function agregar(Request $request): Response
    {
        $estante = new Estante();
        $form = $this->createForm(EstanteType::class, $estante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($estante);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El estante '.$estante->getNumero().' ha sido creado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'El estante '.$estante->getNumero().' no se pudo agregar, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error desconocido: '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
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
     * @return Response
     */
    public function editar(Request $request, Estante $estante): Response
    {
        $form = $this->createForm(EstanteType::class, $estante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El estante '.$estante->getNumero().' ha sido editado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'El estante no se pudo editar, ya existe uno con igual identificador.');
                } else
                {
                    $this->addFlash('error', 'Error desconocido: '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
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
     * @return Response
     */
    public function eliminar(Request $request, Estante $estante): Response
    {
        $form = $this->createDeleteForm($estante);

        if ($request->getMethod() == "DELETE") {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($estante);

                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'El estante '.$estante->getNumero().' ha sido eliminado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1451)  {
                        $this->addFlash('error', 'El estante no se pudo eliminar, conserva expedientes o libros asociados.');
                    } else
                    {
                        $this->addFlash('error', 'Error desconocido: '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
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
    private function createDeleteForm(Estante $estante)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eliminar_estante', array('id' => $estante->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
