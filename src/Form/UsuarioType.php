<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', null, [
                'attr' => ['autofocus' => true, 'class' => 'form-control form-control-border border-width-2 text-sm text-muted', 'autocomplete' => 'off', 'placeholder' => 'Nombre del usuario'],
            ])
            ->add('usuario', null, [
                'attr' => ['class' => 'form-control form-control-border border-width-2 text-sm text-muted', 'autocomplete' => 'off', 'placeholder' => 'Identificador'],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => ['attr' => ['class' => 'form-control form-control-border border-width-2 text-sm text-muted', 'autocomplete' => 'off']],
            ])
            ->add('roles', ChoiceType::class, [
                'multiple' => false,
                'attr' => ['class' => 'form-control form-control-border border-width-2 text-sm text-muted', 'placeholder' => 'Role del usuario'],
                'choices' => [
                    '' => '',
                    'Administrador' => 'ROLE_ADMIN',
                    'Directivo' => 'ROLE_DIRECTIVO',
                    'Técnico Archivista' => 'ROLE_REGISTRO',
                    'Jefe de Sala' => 'ROLE_SALA',
                    'Estacionario' => 'ROLE_ESTACIONARIO',
                    'Esp. en Reprografía' => 'ROLE_REPROGRAFIA',
                    'Esp. en Paleología' => 'ROLE_TRANSCRIPCION',
                    'Esp. en Certificación' => 'ROLE_CERTIFICACION',
                ],
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var $usuario */
                $usuario = $event->getData();

                if ($usuario->getRoles()[0] == 'ROLE_ADMIN') {
                    $usuario->setRuta('admin_listar_usuarios');
                } elseif ($usuario->getRoles()[0] == 'ROLE_SALA') {
                    $usuario->setRuta('sala_listar_investigaciones');
                } elseif ($usuario->getRoles()[0] == 'ROLE_ESTACIONARIO') {
                    $usuario->setRuta('estacionario_listar_investigaciones_nuevas');
                } elseif ($usuario->getRoles()[0] == 'ROLE_REGISTRO') {
                    $usuario->setRuta('registro_listar_expedientes');
                } elseif ($usuario->getRoles()[0] == 'ROLE_DIRECTIVO') {
                    $usuario->setRuta('direccion_listar_cartas_pendientes');
                } elseif ($usuario->getRoles()[0] == 'ROLE_REPROGRAFIA') {
                    $usuario->setRuta('servicio_listar_reprografias');
                } elseif ($usuario->getRoles()[0] == 'ROLE_TRANSCRIPCION') {
                    $usuario->setRuta('servicio_listar_transcripciones');
                } elseif ($usuario->getRoles()[0] == 'ROLE_CERTIFICACION') {
                    $usuario->setRuta('servicio_listar_certificaciones');
                }
            });

        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
            'attr' => ['id' => 'sac-form']
        ]);
    }
}
