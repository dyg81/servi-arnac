<?php

namespace App\Controller\Sala;

use App\Entity\Expediente;
use App\Entity\Investigacion;
use App\Entity\Libro;
use App\Entity\Transcripcion;
use App\Entity\Traza;
use App\Entity\Vitrina;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sala")
 */
class TranscripcionController extends AbstractController
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
     * @Route("/listar-transcripciones", name="sala_listar_transcripciones", methods={"GET"})
     * @return Response
     */
    public function listarTranscripciones(): Response
    {
        return $this->render('sala/listar-transcripciones.html.twig', [
            'transcripciones' => $this->entityManager->getRepository(Transcripcion::class)->obtenerTranscripcionesPorFechaActualizacion(),
        ]);
    }

    /**
     * @Route("/agregar-transcripcion/{investigacion}/{documento}/{tipo}", name="sala_agregar_transcripcion", methods={"GET", "POST"})
     * @param Investigacion $investigacion
     * @param int $documento
     * @param string $tipo
     * @return Response
     */
    public function agregarTranscripcion(Investigacion $investigacion, int $documento, string $tipo): Response
    {
        $transcripcion = new Transcripcion();
        $transcripcion->setCliente($investigacion->getCliente());

        if ($tipo === 'libro') {
            $libro = $this->entityManager->getRepository(Libro::class)->find($documento);

            if ($libro->getVitrina()) {
                $vitrina = $this->entityManager->getRepository(Vitrina::class)->findOneBy(['documento_id' => $libro->getId(), 'documento_tipo' => 'lib']);
                $this->entityManager->remove($vitrina);
            }

            $libro->setVitrina(false);
            $libro->setEstado(Libro::LIB_ESTADO_RESERVADO_PARA_TRANS);
            $this->entityManager->persist($libro);

            $investigacion->removeLibro($libro);

            $transcripcion->setDocumentoTipo('lib');
            $transcripcion->setDocumentoNombre('Libro ' . $libro->getAnno() . ' (Tomo: ' . $libro->getTomo() . ')');
            $transcripcion->setDocumentoFondo($libro->getFondo());
        } elseif ($tipo === 'expediente') {
            $expediente = $this->entityManager->getRepository(Expediente::class)->find($documento);

            if ($expediente->getVitrina()) {
                $vitrina = $this->entityManager->getRepository(Vitrina::class)->findOneBy(['documento_id' => $expediente->getId(), 'documento_tipo' => 'exp']);
                $this->entityManager->remove($vitrina);
            }

            $expediente->setVitrina(false);
            $expediente->setEstado(Expediente::EXP_ESTADO_RESERVADO_PARA_TRANS);
            $this->entityManager->persist($expediente);

            $investigacion->removeExpediente($expediente);

            $transcripcion->setDocumentoTipo('exp');
            $transcripcion->setDocumentoNombre('Expediente ' . $expediente->getNumero());
            $transcripcion->setDocumentoFondo($expediente->getFondo());
        }

        $investigacionLibros = $investigacion->getLibros();
        $investigacionExpedientes = $investigacion->getExpedientes();
        $investigacionVitrina = $this->entityManager->getRepository(Vitrina::class)->findOneBy(['investigacion' => $investigacion->getId()]);

        if (count($investigacionLibros) == 0 and count($investigacionExpedientes) == 0) {
            $investigacion->setEstado(Investigacion::ESTADO_TERMINADO);
            $investigacion->setFechaActualizacion(new DateTime('now'));
        } elseif (count($investigacionLibros) == 0 and count($investigacionExpedientes) == 1 and ($investigacionVitrina)) {
            $investigacion->setEstado(Investigacion::ESTADO_PAUSADO);
            $investigacion->setFechaActualizacion(new DateTime('now'));
        } elseif (count($investigacionLibros) == 1 and count($investigacionExpedientes) == 0 and ($investigacionVitrina)) {
            $investigacion->setEstado(Investigacion::ESTADO_PAUSADO);
            $investigacion->setFechaActualizacion(new DateTime('now'));
        }

        $transcripcion->setDocumentoId($documento);

        $traza = new Traza();
        $traza->setTipo('warning');
        $traza->setUsuario($this->getUser()->getNombre());
        $traza->setContenido('Ha creado una nueva solicitud de transcripción a nombre del cliente ' . $investigacion->getCliente());

        $this->entityManager->persist($traza);
        $this->entityManager->persist($investigacion);
        $this->entityManager->persist($transcripcion);

        try {
            $this->entityManager->flush();
            $this->addFlash('success', 'Transcripción creada correctamente.');
        } catch (Exception $e) {
            $this->addFlash('error', 'Error : ' . $e->getErrorCode() . '. Consulte al grupo de desarrollo.');
        }

        return $this->redirectToRoute('sala_listar_investigaciones');
    }
}
