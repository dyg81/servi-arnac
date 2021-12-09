<?php

namespace App\Form;

use App\Entity\Pais;
use AppBundle\Library\Urlizer\Urlizer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', null, [
                'attr'     => ['autofocus' => true, 'class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'placeholder' => 'Nombre'],
                //'required' => false,
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var $pais */
                $pais = $event->getData();
                if (null !== $paisNombre = $pais->getNombre()) {
                    $pais->setIdentificador(strtolower(Urlizer::urlize($paisNombre)));
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pais::class,
            'attr' => ['id' => 'sac-form']
        ]);
    }
}
