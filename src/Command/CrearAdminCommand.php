<?php

namespace App\Command;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CrearAdminCommand extends Command
{
    protected static $defaultName = 'app:crear-admin';

    private $entityManager;
    private $usuarioRepository;
    private $passwordEncoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        UsuarioRepository $usuarioRepository,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->usuarioRepository = $usuarioRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure()
    {
        $this->setDescription('Crea el usuario administrador inicial.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->usuarioRepository->findOneBy(['usuario' => 'admin'])) {
            $io->error('Ya existe un usuario con el identificador "admin".');

            return Command::FAILURE;
        }

        $question = new Question('Nueva contraseña para admin: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $question->setValidator(function ($password) {
            if (!is_string($password) || trim($password) === '') {
                throw new \RuntimeException('La contraseña no puede estar vacía.');
            }

            return $password;
        });

        $helper = $this->getHelper('question');
        $plainPassword = $helper->ask($input, $output, $question);

        $usuario = new Usuario();
        $usuario
            ->setUsuario('admin')
            ->setRoles(['ROLE_ADMIN'])
            ->setNombre('Administrador del Sistema')
            ->setRuta('admin_listar_usuarios')
            ->setPassword($this->passwordEncoder->encodePassword($usuario, $plainPassword));

        $this->entityManager->persist($usuario);
        $this->entityManager->flush();

        $io->success('Usuario administrador creado correctamente.');

        return Command::SUCCESS;
    }
}
