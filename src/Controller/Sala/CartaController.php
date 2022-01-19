<?php

namespace App\Controller\Sala;

use App\Entity\Carta;
use App\Form\CartaType;
use App\Repository\CartaRepository;
use App\Service\FileUploader;
use AppBundle\Library\Urlizer\Urlizer;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sala")
 */
class CartaController extends AbstractController
{
    /**
     * @Route("/listar-cartas", name="listar_cartas", methods={"GET"})
     * @param CartaRepository $cartaRepository
     * @return Response
     */
    public function listar(CartaRepository $cartaRepository): Response
    {
        return $this->render('sala/cartas/listar.html.twig', [
            'cartas' => $cartaRepository->findByFechaSolicitud(),
        ]);
    }

    /**
     * @Route("/agregar-cartas", name="agregar_carta", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function agregar(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $carta = new Carta();
        $form = $this->createForm(CartaType::class, $carta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $documentoFile */
            $documentoFile = $form->get('documento_file')->getData();

            if ($documentoFile) {
                $newFilename = $fileUploader->upload($documentoFile);
                $carta->setDocumento($newFilename);
            }

            $entityManager->persist($carta);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'La carta se ha agregado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'Carta no agregada, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_cartas'));
        }

        return $this->render('sala/cartas/agregar.html.twig', [
            'carta' => $carta,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editar-cartas/{id}", name="editar_carta", methods={"GET", "POST"})
     * @param Request $request
     * @param Carta $carta
     * @param EntityManagerInterface $entityManager
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function editar(Request $request, Carta $carta, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(CartaType::class, $carta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $documentoFile */
            $documentoFile = $form->get('documento_file')->getData();

            if ($documentoFile) {
                $newFilename = $fileUploader->upload($documentoFile);
                $carta->setDocumento($newFilename);
            }

            try {
                $entityManager->flush();
                $this->addFlash('success', 'La carta se ha sido editado correctamente.');
            } catch (Exception $e) {
                if ($e->getErrorCode() == 1062)  {
                    $this->addFlash('error', 'Carta no editada, ya existe en el sistema.');
                } else
                {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }
            }

            return new ModalRedirectResponse($this->generateUrl('listar_cartas'));
        }

        return $this->render('sala/cartas/editar.html.twig', [
            'carta' => $carta,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/eliminar-cartas/{id}", name="eliminar_carta", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Carta $carta
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function eliminar(Request $request, Carta $carta, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createDeleteForm($carta);

        if ($request->getMethod() == "DELETE") {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->remove($carta);

                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'La solicitud se ha eliminado correctamente.');
                } catch (Exception $e) {
                    $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
                }

                return new ModalRedirectResponse($this->generateUrl('listar_cartas'));
            }
        }

        return $this->render('sala/cartas/eliminar.html.twig', array(
            'carta' => $carta,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Carta $carta
     * @return FormInterface
     */
    private function createDeleteForm(Carta $carta): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eliminar_carta', array('id' => $carta->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
