<?php

namespace App\Form;

use App\Entity\Categoria;
use App\Entity\Cliente;
use App\Entity\Pais;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClienteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', null, [
                'attr'     => ['autofocus' => true, 'class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'placeholder' => 'Nombre(s) y Apellido(s)']
            ])
            ->add('identificacion', null, [
                'attr'     => ['class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'placeholder' => 'Identificación']
            ])
            ->add('direccion', null, [
                'attr'     => ['class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'placeholder' => 'Dirección']
            ])
            ->add('telefono', null, [
                'attr'     => ['class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'placeholder' => 'Teléfono']
            ])
            ->add('correo', null, [
                'attr'     => ['class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'placeholder' => 'Correo']
            ])
            ->add('ocupacion', null, [
                'attr'     => ['class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'placeholder' => 'Ocupación']
            ])
            ->add('pais', null, [
                'class'         => Pais::class,
                'choice_label'  => 'nombre',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.nombre', 'ASC');
                },
                'attr'          => ['class' => 'form-control'],
                'required'      => false
            ])
            ->add('categoria', null, [
                'class'         => Categoria::class,
                'choice_label'  => 'nombre',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.nombre', 'ASC');
                },
                'attr'          => ['class' => 'form-control'],
                'required'      => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cliente::class,
            'attr' => ['id' => 'sac-form']
        ]);
    }
}
