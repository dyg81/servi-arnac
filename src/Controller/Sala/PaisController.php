<?php

namespace App\Controller\Sala;

use App\Entity\Pais;
use App\Entity\Traza;
use App\Form\PaisType;
use Doctrine\ORM\EntityManagerInterface;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Exception;
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/listar-paises", name="sala_listar_paises", methods={"GET"})
     * @return Response
     */
    public function listarPaises(): Response
    {
        return $this->render('sala/pais/listar.html.twig', [
            'paises' => $this->entityManager->getRepository(Pais::class)->findAll(),
        ]);
    }

    /**
     * @Route("/agregar-pais", name="sala_agregar_pais", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function agregarPais(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $pais = new Pais();
            $form = $this->createForm(PaisType::class, $pais);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha creado el país ' . $pais->getNombre());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($pais);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'País agregado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1062) {
                        $this->addFlash('error', 'País no agregado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'Error : ' . $e->getErrorCode() . '. Consulte al grupo de desarrollo.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('sala_listar_paises'));
            }

            return $this->render('sala/pais/agregar.html.twig', [
                'pais' => $pais,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/editar-pais/{id}", name="sala_editar_pais", methods={"GET", "POST"})
     * @param Request $request
     * @param Pais $pais
     * @return Response
     */
    public function editarPais(Request $request, Pais $pais): Response
    {
        if ($request->isXmlHttpRequest()) {
            $oldData = clone $pais;
            $form = $this->createForm(PaisType::class, $pais);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha cambiado el nombre del país ' . $oldData->getNombre() . ' a ' . $pais->getNombre());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($pais);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'País editado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1062) {
                        $this->addFlash('error', 'País no editado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'Error : ' . $e->getErrorCode() . '. Consulte al grupo de desarrollo.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('sala_listar_paises'));
            }

            return $this->render('sala/pais/editar.html.twig', [
                'pais' => $pais,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/eliminar-pais/{id}", name="sala_eliminar_pais", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Pais $pais
     * @return Response
     */
    public function eliminarPais(Request $request, Pais $pais): Response
    {
        if ($request->isXmlHttpRequest()) {
            $form = $this->createDeleteForm($pais);

            if ($request->getMethod() == "DELETE") {
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $traza = new Traza();
                    $traza->setTipo('danger');
                    $traza->setUsuario($this->getUser()->getNombre());
                    $traza->setContenido('Ha eliminado el país ' . $pais->getNombre());

                    $this->entityManager->persist($traza);
                    $this->entityManager->remove($pais);

                    try {
                        $this->entityManager->flush();
                        $this->addFlash('success', 'País eliminado correctamente.');
                    } catch (Exception $e) {
                        if ($e->getErrorCode() == 1451) {
                            $this->addFlash('error', 'País no eliminado, conserva clientes asociados.');
                        } else {
                            $this->addFlash('error', 'Error : ' . $e->getErrorCode() . '. Consulte al grupo de desarrollo.');
                        }
                    }

                    return new ModalRedirectResponse($this->generateUrl('sala_listar_paises'));
                }
            }

            return $this->render('sala/pais/eliminar.html.twig', array(
                'pais' => $pais,
                'form' => $form->createView()
            ));
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @param Pais $pais
     * @return FormInterface
     */
    private function createDeleteForm(Pais $pais): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sala_eliminar_pais', array('id' => $pais->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
