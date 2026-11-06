<?php

namespace App\Form;

use App\Entity\Anaquel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnaquelType extends AbstractType
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
        $anaquel = $event->getData();

        if (null !== $anaquelNumero = $anaquel->getNumero()) {
            if (strlen($anaquelNumero) == 1) {
                $anaquelNumero = '0' . $anaquelNumero;
            }

            $anaquel->setNumero($anaquelNumero);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Anaquel::class,
            'attr' => ['id' => 'sac-form']
        ]);
    }
}
