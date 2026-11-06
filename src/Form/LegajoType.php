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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero', null, [
                'attr' => ['autofocus' => true, 'class' => 'form-control form-control-border border-width-2 text-sm text-muted', 'autocomplete' => 'off', 'placeholder' => 'Número'],
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, array($this, 'submitData'));
    }

    function submitData(FormEvent $event)
    {
        $legajo = $event->getData();

        if (null !== $legajoNumero = $legajo->getNumero()) {
            if (strlen($legajoNumero) == 1) {
                $legajoNumero = '00' . $legajoNumero;
            } elseif (strlen($legajoNumero) == 2) {
                $legajoNumero = '0' . $legajoNumero;
            }

            $legajo->setNumero($legajoNumero);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Legajo::class,
            'attr' => ['id' => 'sac-form']
        ]);
    }
}
