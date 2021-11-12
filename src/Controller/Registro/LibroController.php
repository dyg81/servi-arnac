<?php

namespace App\Controller\Registro;

use App\Entity\Libro;
use App\Form\LibroType;
use App\Repository\LibroRepository;
use Doctrine\DBAL\Exception;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/libros")
 */
class LibroController extends AbstractController
{
    /**
     * @Route("/listar", name="listar_libros", methods={"GET"})
     * @param LibroRepository $libroRepository
     * @return Response
     */
    public function listar(LibroRepository $libroRepository): Response
    {
        return $this->render('registro/libros/listar.html.twig', [
            'libros' => $libroRepository->findAll(),
        ]);
    }

    /**
     * @Route("/agregar", name="agregar_libro", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function agregar(Request $request): Response
    {
        $libro = new Libro();
        $form = $this->createForm(LibroType::class, $libro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($libro);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El libro '.$libro->getTomo().' ha sido creado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'El libro '.$libro->getTomo().' no se pudo agregar, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error desconocido: '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_libros'));
        }

        return $this->render('registro/libros/agregar.html.twig', [
            'libro' => $libro,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editar/{id}", name="editar_libro", methods={"GET","POST"})
     */
    public function editar(Request $request, Libro $libro): Response
    {
        $form = $this->createForm(LibroType::class, $libro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $entityManager->flush();
                $this->addFlash('success', 'El libro '.$libro->getTomo().' ha sido editado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'El libro no se pudo editar, ya existe uno con igual identificador.');
                } else
                {
                    $this->addFlash('error', 'Error desconocido: '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_libros'));
        }

        return $this->render('registro/libros/editar.html.twig', [
            'libro' => $libro,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/eliminar/{id}", name="eliminar_libro", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Libro $libro
     * @return Response
     */
    public function eliminar(Request $request, Libro $libro): Response
    {
        $form = $this->createDeleteForm($libro);

        if ($request->getMethod() == "DELETE") {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($libro);

                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'El libro '.$libro->getTomo().' ha sido eliminado correctamente.');
                } catch (Exception $e) {
                    $this->addFlash('error', 'Error desconocido: '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }

                return new ModalRedirectResponse($this->generateUrl('listar_libros'));
            }
        }

        return $this->render('registro/libros/eliminar.html.twig', array(
            'libro' => $libro,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Libro $libro
     * @return FormInterface
     */
    private function createDeleteForm(Libro $libro)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eliminar_libro', array('id' => $libro->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
