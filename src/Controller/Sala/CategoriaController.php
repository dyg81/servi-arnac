<?php

namespace App\Controller\Sala;

use App\Entity\Categoria;
use App\Form\CategoriaType;
use App\Repository\CategoriaRepository;
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
class CategoriaController extends AbstractController
{
    /**
     * @Route("/listar-categorias", name="listar_categorias", methods={"GET"})
     * @param CategoriaRepository $categoriaRepository
     * @return Response
     */
    public function listar(CategoriaRepository $categoriaRepository): Response
    {
        return $this->render('sala/categorias/listar.html.twig', [
            'categorias' => $categoriaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/agregar-categorias", name="agregar_categoria", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function agregar(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categoria = new Categoria();
        $form = $this->createForm(CategoriaType::class, $categoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categoria);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'La categoria se ha agregado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'Categoria no agregada, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_categorias'));
        }

        return $this->render('sala/categorias/agregar.html.twig', [
            'categoria' => $categoria,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editar-categorias/{id}", name="editar_categoria", methods={"GET","POST"})
     * @param Request $request
     * @param Categoria $categoria
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editar(Request $request, Categoria $categoria, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoriaType::class, $categoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', 'La categoria se ha sido editado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'Categoria no editada, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_categorias'));
        }

        return $this->render('sala/categorias/editar.html.twig', [
            'categoria' => $categoria,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/eliminar-categorias/{id}", name="eliminar_categoria", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Categoria $categoria
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function eliminar(Request $request, Categoria $categoria, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createDeleteForm($categoria);

        if ($request->getMethod() == "DELETE") {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->remove($categoria);

                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'La categoria se ha eliminado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1451)  {
                        $this->addFlash('error', 'Categoria no eliminada, conserva facturas asociadas.');
                    } else
                    {
                        $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('listar_categorias'));
            }
        }

        return $this->render('sala/categorias/eliminar.html.twig', array(
            'categoria' => $categoria,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Categoria $categoria
     * @return FormInterface
     */
    private function createDeleteForm(Categoria $categoria): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eliminar_categoria', array('id' => $categoria->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
