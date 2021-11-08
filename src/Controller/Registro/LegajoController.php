<?php

namespace App\Controller\Registro;

use App\Entity\Legajo;
use App\Form\LegajoType;
use App\Repository\LegajoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/legajos")
 */
class LegajoController extends AbstractController
{
    /**
     * @Route("/listar", name="listar_legajos", methods={"GET"})
     */
    public function index(LegajoRepository $legajoRepository): Response
    {
        return $this->render('registro/legajo/index.html.twig', [
            'legajos' => $legajoRepository->findAllOrderByAsc(),
        ]);
    }

    /**
     * @Route("/new", name="registro_legajo_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $legajo = new Legajo();
        $form = $this->createForm(LegajoType::class, $legajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($legajo);
            $entityManager->flush();

            return $this->redirectToRoute('registro_legajo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('registro/legajo/new.html.twig', [
            'legajo' => $legajo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="registro_legajo_show", methods={"GET"})
     */
    public function show(Legajo $legajo): Response
    {
        return $this->render('registro/legajo/show.html.twig', [
            'legajo' => $legajo,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="registro_legajo_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Legajo $legajo): Response
    {
        $form = $this->createForm(LegajoType::class, $legajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('registro_legajo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('registro/legajo/edit.html.twig', [
            'legajo' => $legajo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="registro_legajo_delete", methods={"POST"})
     */
    public function delete(Request $request, Legajo $legajo): Response
    {
        if ($this->isCsrfTokenValid('delete'.$legajo->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($legajo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('registro_legajo_index', [], Response::HTTP_SEE_OTHER);
    }
}
