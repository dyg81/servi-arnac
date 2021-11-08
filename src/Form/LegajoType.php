<?php

namespace App\Form;

use App\Entity\Legajo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LegajoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('legajo')
            ->add('identificador')
        ;

        $builder
            ->add('legajo', null, [
                'attr'     => ['autofocus' => true, 'class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'placeholder' => 'Legajo'],
                //'required' => false,
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var $legajo */
                $legajo = $event->getData();

                if (null !== $legajoNombre = $legajo->getLegajo()) {
                    if ( intval($legajoNombre) ) {
                        if ( strlen($legajoNombre) == 1 ) {
                            $legajoNombre = '0'.$legajoNombre;
                        }
                        elseif (strlen($legajoNombre) == 2) {
                            $legajoNombre = '00'.$legajoNombre;
                        }
                    }

                    $legajo->setLegajo($legajoNombre);
                    $legajo->setIdentificador('LEG-' . strtoupper(str_replace(' ', '', $legajoNombre)));
                }
            })
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Legajo::class,
            'attr' => ['id' => 'sac-form']
        ]);
    }
}
