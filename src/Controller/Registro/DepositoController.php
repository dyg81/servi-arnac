<?php

namespace App\Controller\Registro;

use App\Entity\Deposito;
use App\Entity\Traza;
use App\Form\DepositoType;
use Doctrine\ORM\EntityManagerInterface;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Exception;
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
     * @Route("/listar-depositos", name="registro_listar_depositos", methods={"GET"})
     * @return Response
     */
    public function listarDepositos(): Response
    {
        return $this->render('registro/deposito/listar.html.twig', [
            'depositos' => $this->entityManager->getRepository(Deposito::class)->findBy(array(), array('numero' => 'ASC')),
        ]);
    }

    /**
     * @Route("/agregar-deposito", name="registro_agregar_deposito", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function agregarDepositos(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $deposito = new Deposito();
            $form = $this->createForm(DepositoType::class, $deposito);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha creado un nuevo depósito con identificador DNAVE' . $deposito->getNumero());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($deposito);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Depósito agregado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1062) {
                        $this->addFlash('error', 'Depósito no agregado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'Error : ' . $e->getErrorCode() . '. Consulte al grupo de desarrollo.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('registro_listar_depositos'));
            }

            return $this->render('registro/deposito/agregar.html.twig', [
                'deposito' => $deposito,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/editar-deposito/{id}", name="registro_editar_deposito", methods={"GET", "POST"})
     * @param Request $request
     * @param Deposito $deposito
     * @return Response
     */
    public function editarDepositos(Request $request, Deposito $deposito): Response
    {
        if ($request->isXmlHttpRequest()) {
            $oldData = clone $deposito;

            $form = $this->createForm(DepositoType::class, $deposito);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha editado el depósito con identificador DNAVE' . $oldData->getNumero() . '. Nuevo identificador DNAVE' . $deposito->getNumero());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($deposito);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Depósito editado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1062) {
                        $this->addFlash('error', 'Depósito no editado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'Error : ' . $e->getErrorCode() . '. Consulte al grupo de desarrollo.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('registro_listar_depositos'));
            }

            return $this->render('registro/deposito/editar.html.twig', [
                'deposito' => $deposito,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/eliminar-deposito/{id}", name="registro_eliminar_deposito", methods={"GET", "DELETE"})
     * @param Request $request
     * @param Deposito $deposito
     * @return Response
     */
    public function eliminarDepositos(Request $request, Deposito $deposito): Response
    {
        if ($request->isXmlHttpRequest()) {
            $form = $this->createDeleteForm($deposito);

            if ($request->getMethod() == "DELETE") {
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $traza = new Traza();
                    $traza->setTipo('danger');
                    $traza->setUsuario($this->getUser()->getNombre());
                    $traza->setContenido('Ha eliminado el depósito con identificador DNAVE' . $deposito->getNumero());

                    $this->entityManager->persist($traza);
                    $this->entityManager->remove($deposito);

                    try {
                        $this->entityManager->flush();
                        $this->addFlash('success', 'Depósito eliminado correctamente.');
                    } catch (Exception $e) {
                        if ($e->getErrorCode() == 1451) {
                            $this->addFlash('error', 'Depósito no eliminado, conserva expedientes o libros asociados.');
                        } else {
                            $this->addFlash('error', 'Error : ' . $e->getErrorCode() . '. Consulte al grupo de desarrollo.');
                        }
                    }

                    return new ModalRedirectResponse($this->generateUrl('registro_listar_depositos'));
                }
            }

            return $this->render('registro/deposito/eliminar.html.twig', array(
                'deposito' => $deposito,
                'form' => $form->createView()
            ));
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @param Deposito $deposito
     * @return FormInterface
     */
    private function createDeleteForm(Deposito $deposito): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('registro_eliminar_deposito', array('id' => $deposito->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Route("/obtener-depositos", name="obtener_depositos_por_fondo", condition="request.isXmlHttpRequest()")
     * @param Request $request
     * @return JsonResponse
     */
    public function obtenerDepositosbyFondo(Request $request): JsonResponse
    {
        $depositosArray = array();
        $depositosAsociados = $this->entityManager->getRepository(Deposito::class)->obtenerDepositosPorFondo($request->query->get('fondoid'));

        foreach ($depositosAsociados as $deposito) {
            $depositosArray[] = array(
                'id' => $deposito->getId(),
                'identificador' => 'DNAVE' . $deposito->getNumero(),
            );
        }

        return new JsonResponse($depositosArray);
    }
}
