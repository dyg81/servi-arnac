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
 * @Route("/legajos")
 */
class LegajoController extends AbstractController
{
    /**
     * @Route("/listar", name="listar_legajos", methods={"GET"})
     * @param LegajoRepository $legajoRepository
     * @return Response
     */
    public function index(LegajoRepository $legajoRepository): Response
    {
        return $this->render('registro/legajos/listar.html.twig', [
            'legajos' => $legajoRepository->findAllOrderByAsc(),
        ]);
    }

    /**
     * @Route("/agregar", name="agregar_legajo", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function agregarLegajo(Request $request): Response
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
                $this->addFlash('error', 'El legajo '.$legajo->getLegajo().' no ha podido ser creado.');
            }

            return new ModalRedirectResponse($this->generateUrl('listar_legajos'));
        }

        return $this->render('registro/legajos/agregar.html.twig', [
            'legajos' => $legajo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editar/{id}", name="editar_legajo", methods={"GET","POST"})
     * @param Request $request
     * @param Legajo $legajo
     * @return Response
     */
    public function editarLegajo(Request $request, Legajo $legajo): Response
    {
        $form = $this->createForm(LegajoType::class, $legajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El legajo '.$legajo->getLegajo().' ha sido editado correctamente.');
            } catch (Exception $e) {
                $this->addFlash('error', 'El legajo '.$legajo->getLegajo().' no ha podido ser editado.');
            }

            return new ModalRedirectResponse($this->generateUrl('listar_legajos'));
        }

        return $this->render('registro/legajos/editar.html.twig', [
            'legajo' => $legajo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/eliminar/{id}", name="eliminar_legajo", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Legajo $legajo
     * @return Response
     */
    public function eliminarLegajo(Request $request, Legajo $legajo): Response
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
                    $this->addFlash('error', 'El legajo '.$legajo->getLegajo().' no ha podido ser eliminado.');
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
