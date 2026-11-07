<?php

namespace App\Controller\Sala;

use App\Entity\Carta;
use App\Entity\Traza;
use App\Form\CartaType;
use App\Service\FileUploader;
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
class CartaController extends AbstractController
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
     * @Route("/listar-cartas", name="sala_listar_cartas", methods={"GET"})
     * @return Response
     */
    public function listarCartas(): Response
    {
        return $this->render('sala/carta/listar.html.twig', [
            'cartas' => $this->entityManager->getRepository(Carta::class)->obtenerCartasOrdenadasPorFechaSolicitud(),
        ]);
    }

    /**
     * @Route("/agregar-carta", name="sala_agregar_carta", methods={"GET", "POST"})
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function agregarCarta(Request $request, FileUploader $fileUploader): Response
    {
        if ($request->isXmlHttpRequest()) {
            $carta = new Carta();
            $form = $this->createForm(CartaType::class, $carta);
            $form->remove('fondos');
            $form->remove('observaciones');
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $documentoFile = $form->get('documento_file')->getData();

                if ($documentoFile) {
                    $newFilename = $fileUploader->upload($documentoFile);
                    $carta->setDocumento($newFilename);
                }

                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha añadido la carta de solicitud del cliente ' . $carta->getCliente());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($carta);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Carta agregada correctamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                }

                return new ModalRedirectResponse($this->generateUrl('sala_listar_cartas'));
            }

            return $this->render('sala/carta/agregar.html.twig', [
                'carta' => $carta,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/editar-carta/{id}", name="sala_editar_carta", methods={"GET", "POST"})
     * @param Request $request
     * @param Carta $carta
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function editarCarta(Request $request, Carta $carta, FileUploader $fileUploader): Response
    {
        if ($request->isXmlHttpRequest()) {
            $form = $this->createForm(CartaType::class, $carta);
            $form->remove('fondos');
            $form->remove('cliente');
            $form->remove('observaciones');
            $form->handleRequest($request);

            if ($request->getMethod() == 'GET') {
                $cliente = $form->getData()->getCliente();
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $documentoFile = $form->get('documento_file')->getData();

                if ($documentoFile) {
                    $newFilename = $fileUploader->upload($documentoFile);
                    $carta->setDocumento($newFilename);
                }

                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha editado la carta de solicitud del cliente ' . $carta->getCliente());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($carta);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Carta editada correctamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                }

                return new ModalRedirectResponse($this->generateUrl('sala_listar_cartas'));
            }

            return $this->render('sala/carta/editar.html.twig', [
                'carta' => $carta,
                'cliente' => $cliente,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/eliminar-carta/{id}", name="sala_eliminar_carta", methods={"GET", "POST"})
     * @param Request $request
     * @param Carta $carta
     * @return Response
     */
    public function eliminarCarta(Request $request, Carta $carta): Response
    {
        if ($request->isXmlHttpRequest()) {
            $form = $this->createDeleteForm($carta);

            if ($request->getMethod() == "POST") {
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $traza = new Traza();
                    $traza->setTipo('danger');
                    $traza->setUsuario($this->getUser()->getNombre());
                    $traza->setContenido('Ha eliminado la carta de solicitud del cliente ' . $carta->getCliente());

                    $this->entityManager->persist($traza);
                    $this->entityManager->remove($carta);

                    try {
                        $this->entityManager->flush();
                        $this->addFlash('success', 'Carta eliminada correctamente.');
                    } catch (Exception $e) {
                        error_log((string) $e);
                        $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                    }

                    return new ModalRedirectResponse($this->generateUrl('sala_listar_cartas'));
                }
            }

            return $this->render('sala/carta/eliminar.html.twig', array(
                'carta' => $carta,
                'form' => $form->createView()
            ));
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @param Carta $carta
     * @return FormInterface
     */
    private function createDeleteForm(Carta $carta): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sala_eliminar_carta', array('id' => $carta->getId())))
            ->setMethod('POST')
            ->getForm();
    }

    /**
     * @Route("/observacion-carta/{id}", name="observacion_carta", methods={"GET"})
     * @param Request $request
     * @param Carta $carta
     * @return Response
     */
    public function observacionCarta(Request $request, Carta $carta): Response
    {
        if ($request->isXmlHttpRequest()) {
            return $this->render('sala/carta/observaciones.html.twig', [
                'mensaje' => $carta->getObservaciones()
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/ver-fondos/{id}", name="sala_ver_fondos")
     * @param Request $request
     * @param Carta $carta
     * @return Response
     */
    public function verFondos(Request $request, Carta $carta): Response
    {
        if ($request->isXmlHttpRequest()) {
            return $this->render('sala/carta/fondos.html.twig', [
                'carta' => $carta,
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }
}
