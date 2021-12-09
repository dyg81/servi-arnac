<?php

namespace App\Controller\Registro;

use App\Entity\Deposito;
use App\Form\DepositoType;
use App\Repository\DepositoRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/registro")
 */
class DepositoController extends AbstractController
{
    /**
     * @Route("/listar-depositos", name="listar_depositos", methods={"GET"})
     * @param DepositoRepository $depositoRepository
     * @return Response
     */
    public function listar(DepositoRepository $depositoRepository): Response
    {
        return $this->render('registro/depositos/listar.html.twig', [
            'depositos' => $depositoRepository->findAllOrderByAsc()
        ]);
    }

    /**
     * @Route("/agregar-depositos", name="agregar_deposito", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function agregar(Request $request, EntityManagerInterface $entityManager): Response
    {
        $deposito = new Deposito();
        $form = $this->createForm(DepositoType::class, $deposito);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($deposito);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El depósito se ha agregado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'Depósito no agregado, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_depositos'));
        }

        return $this->render('registro/depositos/agregar.html.twig', [
            'deposito' => $deposito,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editar-depositos/{id}", name="editar_deposito", methods={"GET","POST"})
     * @param Request $request
     * @param Deposito $deposito
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editar(Request $request, Deposito $deposito, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DepositoType::class, $deposito);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', 'El depósito se ha sido editado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'Depósito no editado, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_depositos'));
        }

        return $this->render('registro/depositos/editar.html.twig', [
            'deposito' => $deposito,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/eliminar-depositos/{id}", name="eliminar_deposito", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Deposito $deposito
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function eliminar(Request $request, Deposito $deposito, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createDeleteForm($deposito);

        if ($request->getMethod() == "DELETE") {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->remove($deposito);

                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'El depósito se ha eliminado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1451)  {
                        $this->addFlash('error', 'Depósito no eliminado, conserva expedientes y/o libros asociados.');
                    } else
                    {
                        $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                    }
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
    private function createDeleteForm(Deposito $deposito): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eliminar_deposito', array('id' => $deposito->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    /**
     * @Route("/obtener-depositos", name="obtener_depositos")
     * @param DepositoRepository $depositoRepository
     * @param Request $request
     * @return JsonResponse
     */
    public function obtenerDepositos(DepositoRepository $depositoRepository, Request $request): JsonResponse
    {
        $depositosArray = array();
        $depositosAsociados = $depositoRepository->findAllByFondo($request->query->get('fondoid'));

        foreach ($depositosAsociados as $deposito) {
            $depositosArray[] = array(
                "id" => $deposito->getId(),
                "numero" => $deposito->getIdentificador(),
            );
        }

        return new JsonResponse($depositosArray);
    }
}