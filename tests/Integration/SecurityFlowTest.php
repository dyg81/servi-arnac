<?php

namespace App\Tests\Integration;

use App\Entity\Usuario;
use App\Entity\Traza;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityFlowTest extends WebTestCase
{
    public function testLoginPageRendersUsernamePasswordAndCsrfFields(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSame(1, $crawler->filter('input[name="_username"]')->count());
        self::assertSame(1, $crawler->filter('input[name="_password"]')->count());
        self::assertSame(1, $crawler->filter('input[name="_csrf_token"]')->count());
    }

    public function testAnonymousUsersAreRedirectedFromProtectedAreas(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/listar-usuarios');
        self::assertResponseRedirects('http://localhost/');

        $client->request('GET', '/sala/listar-expedientes');
        self::assertResponseRedirects('http://localhost/');
    }

    public function testAuthenticatedRoleIsDeniedFromOtherAreaAndInvalidLogoutCsrfDoesNotWrite(): void
    {
        $client = static::createClient();
        $loginPage = $client->request('GET', '/');
        $csrf = $loginPage->filter('input[name="_csrf_token"]')->attr('value');
        $entityManager = static::$container->get('doctrine')->getManager();
        $encoder = static::$container->get('security.password_encoder');

        $user = $entityManager->getRepository(Usuario::class)->findOneBy(['usuario' => 'phase8_sala_user']);
        if (!$user) {
            $user = new Usuario();
            $user->setUsuario('phase8_sala_user');
            $user->setNombre('Usuario de prueba de Fase 8');
        }
        $user->setRoles(['ROLE_SALA']);
        $user->setRuta('sac_login');
        $user->setPassword($encoder->encodePassword($user, 'phase8-test-password'));
        $entityManager->persist($user);
        $entityManager->flush();

        $client->request('POST', '/', [
            '_username' => 'phase8_sala_user',
            '_password' => 'phase8-test-password',
            '_csrf_token' => $csrf,
        ]);
        self::assertResponseRedirects('/');

        $traces = $entityManager->getRepository(Traza::class)->count([]);
        $client->request('GET', '/admin/listar-usuarios');
        self::assertResponseStatusCodeSame(403);
        self::assertSame($traces, $entityManager->getRepository(Traza::class)->count([]));

        $client->request('POST', '/cerrar', ['_csrf_token' => 'invalid-token']);
        self::assertResponseStatusCodeSame(403);
        self::assertSame($traces, $entityManager->getRepository(Traza::class)->count([]));
    }

    public function testLogoutCannotBeTriggeredByGet(): void
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        self::assertResponseStatusCodeSame(405);
    }
}
