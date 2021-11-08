<?php

namespace App\Form;

use App\Entity\Fondo;
use AppBundle\Library\Urlizer\Urlizer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FondoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', null, [
                'attr'     => ['autofocus' => true, 'class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'placeholder' => 'Identificador'],
                //'required' => false,
            ])
            ->add('descripcion', null, [
                'attr'     => ['class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'rows' => '1', 'placeholder' => 'Descripción'],
                //'required' => false,
            ])
            ->add('depositos', null, [
                'attr'     => ['class' => 'form-control'],
                //'required' => false,
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var $fondo */
                $fondo =$event->getData();
                if (null !== $fondoNombre = $fondo->getNombre()) {
                    $fondo->setIdentificador(strtolower(Urlizer::urlize($fondoNombre)));
                }
            })
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Fondo::class,
            'attr' => ['id' => 'sac-form']
        ]);
    }
}
