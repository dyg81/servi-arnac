<?php

namespace App\Controller\Registro;

use App\Entity\Certificacion;
use App\Entity\Libro;
use App\Entity\Reprografia;
use App\Entity\Transcripcion;
use App\Entity\Traza;
use App\Form\LibroType;
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
class LibroController extends AbstractController
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
     * @Route("/listar-libros", name="registro_listar_libros", methods={"GET"})
     * @return Response
     */
    public function listarLibros(): Response
    {
        return $this->render('registro/libro/listar.html.twig', [
            'libros' => $this->entityManager->getRepository(Libro::class)->findAll(),
        ]);
    }

    /**
     * @Route("/agregar-libro", name="registro_agregar_libro", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function agregarLibro(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $libro = new Libro();
            $form = $this->createForm(LibroType::class, $libro);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha agregado un libro con identificador LIB' . $libro->getAnno() . ' Tomo:' . $libro->getTomo() . ' asociado al fondo ' . $libro->getFondo());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($libro);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Libro agregado correctamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    if ($e->getErrorCode() == 1062) {
                        $this->addFlash('error', 'Libro no agregado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('registro_listar_libros'));
            }

            return $this->render('registro/libro/agregar.html.twig', [
                'libro' => $libro,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/editar-libro/{id}", name="registro_editar_libro", methods={"GET", "POST"})
     * @param Request $request
     * @param Libro $libro
     * @return Response
     */
    public function editarLibro(Request $request, Libro $libro): Response
    {
        if ($request->isXmlHttpRequest()) {
            $oldData = clone $libro;
            $form = $this->createForm(LibroType::class, $libro);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha editado el libro con identificador LIB' . $libro->getAnno() . ' Tomo:' . $libro->getTomo() . ' asociado al fondo ' . $libro->getFondo());

                if (($oldData->getAnno() != $libro->getAnno()) or ($oldData->getTomo() != $libro->getTomo()) or ($oldData->getFondo() !== $libro->getFondo())) {
                    $transcripciones = $this->entityManager->getRepository(Transcripcion::class)->findBy(['documento_id' => $libro->getId(), 'documento_tipo' => 'lib']);

                    foreach ($transcripciones as $transcripcion) {
                        $transcripcion->setDocumentoNombre('Libro ' . $libro->getAnno() . ' (Tomo: ' . $libro->getTomo() . ')');
                        $transcripcion->setDocumentoFondo($libro->getFondo());

                        $this->entityManager->persist($transcripcion);
                    }

                    $reprografias = $this->entityManager->getRepository(Reprografia::class)->findBy(['documento_id' => $libro->getId(), 'documento_tipo' => 'lib']);

                    foreach ($reprografias as $reprografia) {
                        $reprografia->setDocumentoNombre('Libro ' . $libro->getAnno() . ' (Tomo: ' . $libro->getTomo() . ')');
                        $reprografia->setDocumentoFondo($libro->getFondo());

                        $this->entityManager->persist($reprografia);
                    }

                    $certificaciones = $this->entityManager->getRepository(Certificacion::class)->findBy(['documento_id' => $libro->getId(), 'documento_tipo' => 'lib']);

                    foreach ($certificaciones as $certificacion) {
                        $certificacion->setDocumentoNombre('Libro ' . $libro->getAnno() . ' (Tomo: ' . $libro->getTomo() . ')');
                        $certificacion->setDocumentoFondo($libro->getFondo());

                        $this->entityManager->persist($certificacion);
                    }
                }

                $this->entityManager->persist($traza);
                $this->entityManager->persist($libro);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Libro editado correctamente.');
                } catch (Exception $e) {
                    error_log((string) $e);
                    if ($e->getErrorCode() == 1062) {
                        $this->addFlash('error', 'Libro no editado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('registro_listar_libros'));
            }

            return $this->render('registro/libro/editar.html.twig', [
                'libro' => $libro,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/eliminar-libro/{id}", name="registro_eliminar_libro", methods={"GET", "POST"})
     * @param Request $request
     * @param Libro $libro
     * @return Response
     */
    public function eliminarLibro(Request $request, Libro $libro): Response
    {
        if ($request->isXmlHttpRequest()) {
            $form = $this->createDeleteForm($libro);

            if ($request->getMethod() == "POST") {
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    if ($this->tieneSolicitudesDeServicio($libro->getId())) {
                        $this->addFlash('error', 'No se puede eliminar el libro porque tiene solicitudes de servicio asociadas.');

                        return new ModalRedirectResponse($this->generateUrl('registro_listar_libros'));
                    }

                    $traza = new Traza();
                    $traza->setTipo('danger');
                    $traza->setUsuario($this->getUser()->getNombre());
                    $traza->setContenido('Ha eliminado el libro con identificador LIB' . $libro->getAnno() . ' Tomo:' . $libro->getTomo() . ' asociado al fondo ' . $libro->getFondo());

                    $this->entityManager->persist($traza);
                    $this->entityManager->remove($libro);

                    try {
                        $this->entityManager->flush();
                        $this->addFlash('success', 'Libro eliminado correctamente.');
                    } catch (Exception $e) {
                        error_log((string) $e);
                        $this->addFlash('error', 'No se pudo completar la operación. Intente nuevamente o contacte al equipo de soporte.');
                    }

                    return new ModalRedirectResponse($this->generateUrl('registro_listar_libros'));
                }
            }

            return $this->render('registro/libro/eliminar.html.twig', array(
                'libro' => $libro,
                'form' => $form->createView()
            ));
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @param Libro $libro
     * @return FormInterface
     */
    private function createDeleteForm(Libro $libro): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('registro_eliminar_libro', array('id' => $libro->getId())))
            ->setMethod('POST')
            ->getForm();
    }

    private function tieneSolicitudesDeServicio(int $documentoId): bool
    {
        foreach ([Transcripcion::class, Reprografia::class, Certificacion::class] as $serviceClass) {
            if ($this->entityManager->getRepository($serviceClass)->findOneBy([
                'documento_id' => $documentoId,
                'documento_tipo' => 'lib',
            ])) {
                return true;
            }
        }

        return false;
    }
}
