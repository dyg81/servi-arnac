<?php

namespace App\Controller\Sala;

use App\Entity\Cliente;
use App\Entity\Traza;
use App\Form\ClienteType;
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
class ClienteController extends AbstractController
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
     * @Route("/listar-clientes", name="sala_listar_clientes", methods={"GET"})
     * @return Response
     */
    public function listarClientes(): Response
    {
        return $this->render('sala/cliente/listar.html.twig', [
            'clientes' => $this->entityManager->getRepository(Cliente::class)->findBy(array(), array('id' => 'DESC')),
        ]);
    }

    /**
     * @Route("/agregar-cliente", name="sala_agregar_cliente", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function agregarCliente(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $cliente = new Cliente();
            $form = $this->createForm(ClienteType::class, $cliente);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha creado el cliente ' . $cliente->getNombre() . ' con número de identificación ' . $cliente->getIdentificacion());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($cliente);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Cliente agregado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1062) {
                        $this->addFlash('error', 'Cliente no agregado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'Error : ' . $e->getErrorCode() . '. Consulte al grupo de desarrollo.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('sala_listar_clientes'));
            }

            return $this->render('sala/cliente/agregar.html.twig', [
                'cliente' => $cliente,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route("/editar-cliente/{id}", name="sala_editar_cliente", methods={"GET", "POST"})
     * @param Request $request
     * @param Cliente $cliente
     * @return Response
     */
    public function editarCliente(Request $request, Cliente $cliente): Response
    {
        if ($request->isXmlHttpRequest()) {
            $form = $this->createForm(ClienteType::class, $cliente);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $traza = new Traza();
                $traza->setTipo('warning');
                $traza->setUsuario($this->getUser()->getNombre());
                $traza->setContenido('Ha editado el cliente ' . $cliente->getNombre() . ' con número de identificación ' . $cliente->getIdentificacion());

                $this->entityManager->persist($traza);
                $this->entityManager->persist($cliente);

                try {
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Cliente editado correctamente.');
                } catch (Exception $e) {
                    if ($e->getErrorCode() == 1062) {
                        $this->addFlash('error', 'Cliente no editado, ya existe en el sistema.');
                    } else {
                        $this->addFlash('error', 'Error : ' . $e->getErrorCode() . '. Consulte al grupo de desarrollo.');
                    }
                }

                return new ModalRedirectResponse($this->generateUrl('sala_listar_clientes'));
            }

            return $this->render('sala/cliente/editar.html.twig', [
                'cliente' => $cliente,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }
}
