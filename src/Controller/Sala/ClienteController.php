<?php

namespace App\Controller\Sala;

use App\Entity\Cliente;
use App\Form\ClienteType;
use App\Repository\ClienteRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sala")
 */
class ClienteController extends AbstractController
{
    /**
     * @Route("/listar-clientes", name="listar_clientes", methods={"GET"})
     * @param ClienteRepository $clienteRepository
     * @return Response
     */
    public function listar(ClienteRepository $clienteRepository): Response
    {
        return $this->render('sala/clientes/listar.html.twig', [
            'clientes' => $clienteRepository->findAll(),
        ]);
    }

    /**
     * @Route("/agregar-clientes", name="agregar_cliente", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function agregar(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cliente = new Cliente();
        $form = $this->createForm(ClienteType::class, $cliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cliente);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El cliente se ha agregado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'Cliente no agregado, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_clientes'));
        }

        return $this->render('sala/clientes/agregar.html.twig', [
            'cliente' => $cliente,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editar-cliente/{id}", name="editar_cliente", methods={"GET", "POST"})
     * @param Request $request
     * @param Cliente $cliente
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editar(Request $request, Cliente $cliente, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClienteType::class, $cliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', 'El cliente se ha sido editado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'Cliente no editado, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_clientes'));
        }

        return $this->render('sala/clientes/editar.html.twig', [
            'cliente' => $cliente,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/eliminar-clientes/{id}", name="eliminar_cliente", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Cliente $cliente
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function eliminar(Request $request, Cliente $cliente, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createDeleteForm($cliente);

        if ($request->getMethod() == "DELETE") {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->remove($cliente);

                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'El cliente se ha eliminado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1451)  {
                        $this->addFlash('error', 'Cliente no eliminado, conserva facturas asociadas.');
                    } else
                    {
                        $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('listar_clientes'));
            }
        }

        return $this->render('sala/clientes/eliminar.html.twig', array(
            'cliente' => $cliente,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Cliente $cliente
     * @return FormInterface
     */
    private function createDeleteForm(Cliente $cliente): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eliminar_cliente', array('id' => $cliente->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
