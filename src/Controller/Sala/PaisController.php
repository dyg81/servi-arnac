<?php

namespace App\Controller\Sala;

use App\Entity\Pais;
use App\Form\PaisType;
use App\Repository\PaisRepository;
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
class PaisController extends AbstractController
{
    /**
     * @Route("/listar-paises", name="listar_paises", methods={"GET"})
     * @param PaisRepository $paisRepository
     * @return Response
     */
    public function listar(PaisRepository $paisRepository): Response
    {
        return $this->render('sala/paises/listar.html.twig', [
            'paises' => $paisRepository->findAllOrderByAsc(),
        ]);
    }

    /**
     * @Route("/agregar-paises", name="agregar_pais", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function agregar(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pais = new Pais();
        $form = $this->createForm(PaisType::class, $pais);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pais);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El país se ha agregado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'País no agregado, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_paises'));
        }

        return $this->render('sala/paises/agregar.html.twig', [
            'pais' => $pais,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editar-paises/{id}", name="editar_pais", methods={"GET","POST"})
     * @param Request $request
     * @param Pais $pais
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editar(Request $request, Pais $pais, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PaisType::class, $pais);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', 'El país se ha sido editado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'País no editado, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_paises'));
        }

        return $this->render('sala/paises/editar.html.twig', [
            'pais' => $pais,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/eliminar-paises/{id}", name="eliminar_pais", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Pais $pais
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function eliminar(Request $request, Pais $pais, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createDeleteForm($pais);

        if ($request->getMethod() == "DELETE") {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->remove($pais);

                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'El país se ha eliminado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1451)  {
                        $this->addFlash('error', 'País no eliminado, conserva clientes asociados.');
                    } else
                    {
                        $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('listar_paises'));
            }
        }

        return $this->render('sala/paises/eliminar.html.twig', array(
            'pais' => $pais,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Pais $pais
     * @return FormInterface
     */
    private function createDeleteForm(Pais $pais): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eliminar_pais', array('id' => $pais->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
