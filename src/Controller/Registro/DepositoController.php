<?php

namespace App\Controller\Registro;

use App\Entity\Deposito;
use App\Form\DepositoType;
use App\Repository\DepositoRepository;
use Doctrine\DBAL\Exception;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/depositos")
 */
class DepositoController extends AbstractController
{
    /**
     * @Route("/listar", name="listar_depositos", methods={"GET"})
     * @param DepositoRepository $depositoRepository
     * @return Response
     */
    public function listarDepositos(DepositoRepository $depositoRepository): Response
    {
        return $this->render('registro/depositos/listar.html.twig', [
            'depositos' => $depositoRepository->findAllOrderByAsc()
        ]);
    }

    /**
     * @Route("/agregar", name="agregar_deposito", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function agregarDeposito(Request $request): Response
    {
        $deposito = new Deposito();
        $form = $this->createForm(DepositoType::class, $deposito);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($deposito);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El depósito '.$deposito->getNumero().' ha sido creado correctamente.');
            } catch (Exception $e) {
                $this->addFlash('error', 'El depósito '.$deposito->getNumero().' no ha podido ser creado.');
            }

            return new ModalRedirectResponse($this->generateUrl('listar_depositos'));

            //return $this->redirectToRoute('listar_depositos', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('registro/depositos/agregar.html.twig', [
            'deposito' => $deposito,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editar/{id}", name="editar_deposito", methods={"GET","POST"})
     */
    public function editarDeposito(Request $request, Deposito $deposito): Response
    {
        $form = $this->createForm(DepositoType::class, $deposito);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El depósito '.$deposito->getNumero().' ha sido editado correctamente.');
            } catch (Exception $e) {
                $this->addFlash('error', 'El depósito '.$deposito->getNumero().' no ha podido ser editado.');
            }

            return new ModalRedirectResponse($this->generateUrl('listar_depositos'));
        }

        return $this->render('registro/depositos/editar.html.twig', [
            'deposito' => $deposito,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/eliminar/{id}", name="eliminar_deposito", methods={"GET", "DELETE"})
     */
    public function eliminarDeposito(Request $request, Deposito $deposito): Response
    {
        $form = $this->createDeleteForm($deposito);

        if ($request->getMethod() == "DELETE") {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($deposito);

                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'El depósito '.$deposito->getNumero().' ha sido eliminado correctamente.');
                } catch (Exception $e) {
                    $this->addFlash('error', 'El depósito '.$deposito->getNumero().' no ha podido ser eliminado.');
                }

                return new ModalRedirectResponse($this->generateUrl('listar_depositos'));
            }
        }

        return $this->render('registro/depositos/eliminar.html.twig', array(
            'deposito' => $deposito,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Deposito $deposito
     * @return FormInterface
     */
    private function createDeleteForm(Deposito $deposito)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eliminar_deposito', array('id' => $deposito->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}