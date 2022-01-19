<?php

namespace App\Form;

use App\Entity\Carta;
use App\Entity\Fondo;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cliente', null, [
                'attr'     => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('documento_file', FileType::class, [
                'mapped'   => false,
                'required' => false
            ])
            ->add('fondos', null, [
                'class'         => Fondo::class,
                'choice_label'  => 'nombre',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('f');
                },
                'attr'          => ['class' => 'form-control'],
                'required'      => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Carta::class,
            'attr' => ['id' => 'sac-form']
        ]);
    }
}
