<?php

namespace App\Form;

use App\Entity\Deposito;
use App\Entity\Fondo;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FondoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', null, [
                'attr' => ['autofocus' => true, 'class' => 'form-control form-control-border border-width-2 text-sm text-muted', 'autocomplete' => 'off', 'placeholder' => 'Nombre'],
            ])
            ->add('contenido', null, [
                'attr' => ['class' => 'form-control form-control-border border-width-2 text-sm text-muted', 'autocomplete' => 'off', 'placeholder' => 'Contenido'],
            ])
            ->add('depositos', null, [
                'class' => Deposito::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')->orderBy('d.numero', 'ASC');
                },
                'attr' => ['class' => 'form-control'],
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fondo::class,
            'attr' => ['id' => 'sac-form']
        ]);
    }
}
