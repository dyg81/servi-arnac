<?php

namespace App\Controller\Registro;

use App\Entity\Fondo;
use App\Form\FondoType;
use App\Repository\FondoRepository;
use Doctrine\DBAL\Exception;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
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
    public function index(FondoRepository $fondoRepository): Response
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
    public function agregarFondo(Request $request): Response
    {
        $fondo = new Fondo();
        $form = $this->createForm(FondoType::class, $fondo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fondo);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El fondo '.$fondo->getNombre().' ha sido creado correctamente.');
            } catch (Exception $e) {
                $this->addFlash('error', 'El fondo '.$fondo->getNombre().' no ha podido ser creado.');
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
     */
    public function edit(Request $request, Fondo $fondo): Response
    {
        $form = $this->createForm(FondoType::class, $fondo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El fondo '.$fondo->getNombre().' ha sido editado correctamente.');
            } catch (Exception $e) {
                $this->addFlash('error', 'El fondo '.$fondo->getNombre().' no ha podido ser editado.');
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
    public function eliminarFondo(Request $request, Fondo $fondo): Response
    {
        $form = $this->createDeleteForm($fondo);

        if ($request->getMethod() == "DELETE") {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($fondo);

                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'El fondo '.$fondo->getNombre().' ha sido eliminado correctamente.');
                } catch (Exception $e) {
                    $this->addFlash('error', 'El fondo '.$fondo->getNombre().' no ha podido ser eliminado.');
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
            ->getForm()
            ;
    }
}
