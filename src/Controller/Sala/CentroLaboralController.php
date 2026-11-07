<?php

namespace App\Controller\Sala;

use App\Entity\CentroLaboral;
use App\Entity\Traza;
use App\Form\CentroLaboralType;
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
class CentroLaboralController extends AbstractController
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
     * @Route("/listar-centros-laborales", name="sala_listar_centros_laborales", methods={"GET"})
     * @return Response
     */
    public function listarCentroLaboral(): Response
    {
        return $this->render('sala/centro_laboral/listar.html.twig', [
            'centros' => $this->entityManager->getRepository(CentroLaboral::class)->findAll(),
        ]);
    }

    /**
     * @Route("/agregar-centro-laboral", name="sala_agregar_centro_laboral", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function agregarCentroLaboral(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $centroLaboral = new CentroLaboral();
            $form = $this->createForm(CentroLaboralType::class, $centroLaboral);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha creado el centro laboral ' . $centroLaboral->getNombre());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($centroLaboral);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Centro laboral agregado correctamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    if ($e->getErrorCode() == 1062) {
                        $this->addFlash('error', 'Centro laboral no agregado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('sala_listar_centros_laborales'));
            }

            return $this->render('sala/centro_laboral/agregar.html.twig', [
                'centro' => $centroLaboral,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/editar-centro-laboral/{id}", name="sala_editar_centro_laboral", methods={"GET", "POST"})
     * @param Request $request
     * @param CentroLaboral $centroLaboral
     * @return Response
     */
    public function editarCentroLaboral(Request $request, CentroLaboral $centroLaboral): Response
    {
        if ($request->isXmlHttpRequest()) {
            $oldData = clone $centroLaboral;
            $form = $this->createForm(CentroLaboralType::class, $centroLaboral);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha editado el nombre del centro laboral ' . $oldData->getNombre() . ' a ' . $centroLaboral->getNombre());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($centroLaboral);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Centro laboral editado correctamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    if ($e->getErrorCode() == 1062) {
                        $this->addFlash('error', 'Centro laboral no editado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('sala_listar_centros_laborales'));
            }

            return $this->render('sala/centro_laboral/editar.html.twig', [
                'centro' => $centroLaboral,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/eliminar-centro-laboral/{id}", name="sala_eliminar_centro_laboral", methods={"GET", "POST"})
     * @param Request $request
     * @param CentroLaboral $centroLaboral
     * @return Response
     */
    public function eliminarCentroLaboral(Request $request, CentroLaboral $centroLaboral): Response
    {
        if ($request->isXmlHttpRequest()) {
            $form = $this->createDeleteForm($centroLaboral);

            if ($request->getMethod() == "POST") {
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $traza = new Traza();
                    $traza->setTipo('danger');
                    $traza->setUsuario($this->getUser()->getNombre());
                    $traza->setContenido('Ha eliminado el centro laboral ' . $centroLaboral->getNombre());

                    $this->entityManager->persist($traza);
                    $this->entityManager->remove($centroLaboral);

                    try {
                        $this->entityManager->flush();
                        $this->addFlash('success', 'Centro laboral eliminado correctamente.');
                    } catch (Exception $e) {
                        error_log((string) $e);
                        if ($e->getErrorCode() == 1451) {
                            $this->addFlash('error', 'Centro laboral no eliminado, conserva clientes asociados.');
                        } else {
                            $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                        }
                    }

                    return new ModalRedirectResponse($this->generateUrl('sala_listar_centros_laborales'));
                }
            }

            return $this->render('sala/centro_laboral/eliminar.html.twig', array(
                'centro' => $centroLaboral,
                'form' => $form->createView()
            ));
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @param CentroLaboral $centroLaboral
     * @return FormInterface
     */
    private function createDeleteForm(CentroLaboral $centroLaboral): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sala_eliminar_centro_laboral', array('id' => $centroLaboral->getId())))
            ->setMethod('POST')
            ->getForm();
    }
}
