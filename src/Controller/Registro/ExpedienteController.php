<?php

namespace App\Controller\Registro;

use App\Entity\Expediente;
use App\Form\ExpedienteType;
use App\Repository\ExpedienteRepository;
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
class ExpedienteController extends AbstractController
{
    /**
     * @Route("/listar-expedientes", name="listar_expedientes", methods={"GET"})
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
     * @Route("/agregar-expedientes", name="agregar_expediente", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function agregar(Request $request, EntityManagerInterface $entityManager): Response
    {
        $expediente = new Expediente();
        $form = $this->createForm(ExpedienteType::class, $expediente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($expediente);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El expediente se ha agregado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'Expediente no agregado, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_expedientes'));
        }

        return $this->render('registro/expedientes/agregar.html.twig', [
            'expediente' => $expediente,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editar-expedientes/{id}", name="editar_expediente", methods={"GET","POST"})
     * @param Request $request
     * @param Expediente $expediente
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editar(Request $request, Expediente $expediente, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExpedienteType::class, $expediente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', 'El expediente se ha sido editado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'Expediente no editado, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_expedientes'));
        }

        return $this->render('registro/expedientes/editar.html.twig', [
            'expediente' => $expediente,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/eliminar-expedientes/{id}", name="eliminar_expediente", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Expediente $expediente
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function eliminar(Request $request, Expediente $expediente, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createDeleteForm($expediente);

        if ($request->getMethod() == "DELETE") {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->remove($expediente);

                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'El expediente se ha eliminado correctamente.');
                } catch (Exception $e) {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
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
    private function createDeleteForm(Expediente $expediente): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eliminar_expediente', array('id' => $expediente->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
