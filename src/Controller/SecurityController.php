<?php

namespace App\Controller;

use App\Entity\Traza;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="sac_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="sac_logout")
     */
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/cerrar", name="sac_cerrar")
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function cerrar(EntityManagerInterface $entityManager): RedirectResponse
    {
        $traza = new Traza();

        $user = $this->getUser();
        $traza->setTipo('warning');
        $traza->setUsuario($user->getNombre());
        $traza->setContenido('Ha cerrado sesión en el sistema.');

        $entityManager->persist($traza);
        $entityManager->flush();

        return $this->redirectToRoute('sac_logout');
    }
}