<?php

namespace App\Form;

use App\Entity\Investigacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvestigacionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cliente', null, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('fondo', null, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('expedientes')
            ->add('libros');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Investigacion::class,
            'attr' => ['id' => 'sac-form'],
            'allow_extra_fields' => true,
        ]);
    }
}
