<?php

namespace App\Controller\Direccion;

use App\Entity\Carta;
use App\Form\CartaType;
use App\Repository\CartaRepository;
use App\Service\FileUploader;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/direccion")
 */
class DireccionController extends AbstractController
{
    /**
     * @Route("/listar-cartas-pendientes", name="listar_cartas_pendientes")
     * @param CartaRepository $cartaRepository
     * @return Response
     */
    public function listarPendientes(CartaRepository $cartaRepository): Response
    {
        return $this->render('direccion/cartas_pendientes.html.twig', [
            'cartas' => $cartaRepository->findByPendientes(),
        ]);
    }

    /**
     * @Route("/listar-cartas-respondidas", name="listar_cartas_respondidas")
     * @param CartaRepository $cartaRepository
     * @return Response
     */
    public function listarRespondidas(CartaRepository $cartaRepository): Response
    {
        return $this->render('direccion/cartas_respondidas.html.twig', [
            'cartas' => $cartaRepository->findByRespondidas(),
        ]);
    }

    /**
     * @Route("/aprobar-cartas/{id}", name="aprobar_carta_pendiente", methods={"GET", "POST"})
     * @param Request $request
     * @param Carta $carta
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function aprobar(Request $request, Carta $carta, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CartaType::class, $carta);
        $form->remove('cliente');
        $form->remove('documento_file');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $carta->setFechaRespuesta(new \DateTime('now'));
            $carta->setEstado(1);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'La carta ha sido aprobada satisfactoriamente.');
            } catch (Exception $e) {
                $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
            }

            return new ModalRedirectResponse($this->generateUrl('listar_cartas_pendientes'));
        }

        return $this->render('direccion/aprobar_carta.html.twig', [
            'carta' => $carta,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/denegar-cartas/{id}", name="denegar_carta_pendiente", methods={"GET", "POST"})
     * @param Request $request
     * @param Carta $carta
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function denegar(Request $request, Carta $carta, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CartaType::class, $carta);
        $form->remove('cliente');
        $form->remove('documento_file');
        $form->remove('fondos');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $carta->setFechaRespuesta(new \DateTime('now'));
            $carta->setEstado(2);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'La carta ha sido aprobada satisfactoriamente.');
            } catch (Exception $e) {
                $this->addFlash('error', 'Error : '.$e->getErrorCode().'. Consulte al grupo de desarrollo.');
            }

            return new ModalRedirectResponse($this->generateUrl('listar_cartas_pendientes'));
        }

        return $this->render('direccion/denegar_carta.html.twig', [
            'carta' => $carta,
            'form' => $form->createView(),
        ]);
    }
}
