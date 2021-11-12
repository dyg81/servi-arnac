<?php

namespace App\Controller\Registro;

use App\Entity\Expediente;
use App\Form\ExpedienteType;
use App\Repository\ExpedienteRepository;
use Doctrine\DBAL\Exception;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/expedientes")
 */
class ExpedienteController extends AbstractController
{
    /**
     * @Route("/listar", name="listar_expedientes", methods={"GET"})
     * @param ExpedienteRepository $expedienteRepository
     * @return Response
     */
    public function listar(ExpedienteRepository $expedienteRepository): Response
    {
        return $this->render('registro/expedientes/listar.html.twig', [
            'expedientes' => $expedienteRepository->findAll(),
        ]);
    }

    /**
     * @Route("/agregar", name="agregar_expediente", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function agregar(Request $request): Response
    {
        $expediente = new Expediente();
        $form = $this->createForm(ExpedienteType::class, $expediente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($expediente);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El expediente '.$expediente->getNumero().' ha sido creado correctamente.');
            } catch (Exception $e) {
                $this->addFlash('error', 'El expediente '.$expediente->getNumero().' no ha podido ser creado.');
            }

            return new ModalRedirectResponse($this->generateUrl('listar_expedientes'));
        }

        return $this->render('registro/expedientes/agregar.html.twig', [
            'expediente' => $expediente,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editar/{id}", name="editar_expediente", methods={"GET","POST"})
     * @param Request $request
     * @param Expediente $expediente
     * @return Response
     */
    public function editar(Request $request, Expediente $expediente): Response
    {
        $form = $this->createForm(ExpedienteType::class, $expediente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El expediente '.$expediente->getNumero().' ha sido editado correctamente.');
            } catch (Exception $e) {
                $this->addFlash('error', 'El expediente '.$expediente->getNumero().' no ha podido ser editado.');
            }

            return new ModalRedirectResponse($this->generateUrl('listar_expedientes'));
        }

        return $this->render('registro/expedientes/editar.html.twig', [
            'expediente' => $expediente,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/eliminar/{id}", name="eliminar_expediente", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Expediente $expediente
     * @return Response
     */
    public function eliminar(Request $request, Expediente $expediente): Response
    {
        $form = $this->createDeleteForm($expediente);

        if ($request->getMethod() == "DELETE") {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($expediente);

                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'El expediente '.$expediente->getNumero().' ha sido eliminado correctamente.');
                } catch (Exception $e) {
                    $this->addFlash('error', 'El expediente '.$expediente->getNumero().' no ha podido ser eliminado.');
                }

                return new ModalRedirectResponse($this->generateUrl('listar_expedientes'));
            }
        }

        return $this->render('registro/expedientes/eliminar.html.twig', array(
            'expediente' => $expediente,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Expediente $expediente
     * @return FormInterface
     */
    private function createDeleteForm(Expediente $expediente)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eliminar_expediente', array('id' => $expediente->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}