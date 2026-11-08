<?php

namespace App\Controller\Registro;

use App\Entity\Certificacion;
use App\Entity\Expediente;
use App\Entity\Reprografia;
use App\Entity\Transcripcion;
use App\Entity\Traza;
use App\Form\ExpedienteType;
use Doctrine\ORM\EntityManagerInterface;
use Dyg81\ModalBundle\Response\ModalRedirectResponse;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/registro")
 */
class ExpedienteController extends AbstractController
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
     * @Route("/listar-expedientes", name="registro_listar_expedientes", methods={"GET"})
     * @return Response
     */
    public function listarExpedientes(): Response
    {
        return $this->render('registro/expediente/listar.html.twig', [
            'expedientes' => $this->entityManager->getRepository(Expediente::class)->findAll(),
        ]);
    }

    /**
     * @Route("/agregar-expediente", name="registro_agregar_expediente", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function agregarExpediente(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $expediente = new Expediente();
            $form = $this->createForm(ExpedienteType::class, $expediente);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha creado un nuevo expediente con identificador EXP' . $expediente->getNumero() . ' asociado al fondo ' . $expediente->getFondo());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($expediente);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Expediente agregado correctamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    if (\App\Service\PersistenceExceptionClassifier::isUniqueConstraintViolation($e)) {
                        $this->addFlash('error', 'Expediente no agregado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('registro_listar_expedientes'));
            }

            return $this->render('registro/expediente/agregar.html.twig', [
                'expediente' => $expediente,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/editar-expediente/{id}", name="registro_editar_expediente", methods={"GET", "POST"})
     * @param Request $request
     * @param Expediente $expediente
     * @return Response
     */
    public function editarExpediente(Request $request, Expediente $expediente): Response
    {
        if ($request->isXmlHttpRequest()) {
            $oldData = clone $expediente;
            $form = $this->createForm(ExpedienteType::class, $expediente);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha editado el expediente con identificador EXP' . $expediente->getNumero() . ' asociado al fondo ' . $expediente->getFondo());

                if (($oldData->getNumero() != $expediente->getNumero()) or ($oldData->getFondo() !== $expediente->getFondo())) {
                    $transcripciones = $this->entityManager->getRepository(Transcripcion::class)->findBy(['documento_id' => $expediente->getId(), 'documento_tipo' => 'exp']);

                    foreach ($transcripciones as $transcripcion) {
                        $transcripcion->setDocumentoNombre('Expediente ' . $expediente->getNumero());
                        $transcripcion->setDocumentoFondo($expediente->getFondo());

                        $this->entityManager->persist($transcripcion);
                    }

                    $reprografias = $this->entityManager->getRepository(Reprografia::class)->findBy(['documento_id' => $expediente->getId(), 'documento_tipo' => 'exp']);

                    foreach ($reprografias as $reprografia) {
                        $reprografia->setDocumentoNombre('Expediente ' . $expediente->getNumero());
                        $reprografia->setDocumentoFondo($expediente->getFondo());

                        $this->entityManager->persist($reprografia);
                    }

                    $certificaciones = $this->entityManager->getRepository(Certificacion::class)->findBy(['documento_id' => $expediente->getId(), 'documento_tipo' => 'exp']);

                    foreach ($certificaciones as $certificacion) {
                        $certificacion->setDocumentoNombre('Expediente ' . $expediente->getNumero());
                        $certificacion->setDocumentoFondo($expediente->getFondo());

                        $this->entityManager->persist($certificacion);
                    }
                }

                $this->entityManager->persist($traza);
                $this->entityManager->persist($expediente);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Expediente editado correctamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    if (\App\Service\PersistenceExceptionClassifier::isUniqueConstraintViolation($e)) {
                        $this->addFlash('error', 'Expediente no editado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('registro_listar_expedientes'));
            }

            return $this->render('registro/expediente/editar.html.twig', [
                'expediente' => $expediente,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/eliminar-expediente/{id}", name="registro_eliminar_expediente", methods={"GET", "POST"})
     * @param Request $request
     * @param Expediente $expediente
     * @return Response
     */
    public function eliminarExpediente(Request $request, Expediente $expediente): Response
    {
        if ($request->isXmlHttpRequest()) {
            $form = $this->createDeleteForm($expediente);

            if ($request->getMethod() == "POST") {
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    if ($this->tieneSolicitudesDeServicio($expediente->getId())) {
                        $this->addFlash('error', 'No se puede eliminar el expediente porque tiene solicitudes de servicio asociadas.');

                        return new ModalRedirectResponse($this->generateUrl('registro_listar_expedientes'));
                    }

                    $traza = new Traza();
                    $traza->setTipo('danger');
                    $traza->setUsuario($this->getUser()->getNombre());
                    $traza->setContenido('Ha eliminado el expediente con identificador EXP' . $expediente->getNumero() . ' asociado al fondo ' . $expediente->getFondo());

                    $this->entityManager->persist($traza);
                    $this->entityManager->remove($expediente);

                    try {
                        $this->entityManager->flush();
                        $this->addFlash('success', 'Expediente eliminado correctamente.');
                    } catch (Exception $e) {
                        error_log((string) $e);
                        $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                    }

                    return new ModalRedirectResponse($this->generateUrl('registro_listar_expedientes'));
                }
            }

            return $this->render('registro/expediente/eliminar.html.twig', array(
                'expediente' => $expediente,
                'form' => $form->createView()
            ));
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @param Expediente $expediente
     * @return FormInterface
     */
    private function createDeleteForm(Expediente $expediente): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('registro_eliminar_expediente', array('id' => $expediente->getId())))
            ->setMethod('POST')
            ->getForm();
    }

    private function tieneSolicitudesDeServicio(int $documentoId): bool
    {
        foreach ([Transcripcion::class, Reprografia::class, Certificacion::class] as $serviceClass) {
            if ($this->entityManager->getRepository($serviceClass)->findOneBy([
                'documento_id' => $documentoId,
                'documento_tipo' => 'exp',
            ])) {
                return true;
            }
        }

        return false;
    }
}
