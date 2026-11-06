<?php

namespace App\Form;

use App\Entity\Estante;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EstanteType extends AbstractType
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
        $estante = $event->getData();

        if (null !== $estanteNumero = $estante->getNumero()) {
            if (strlen($estanteNumero) == 1) {
                $estanteNumero = '0' . $estanteNumero;
            }

            $estante->setNumero($estanteNumero);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Estante::class,
            'attr' => ['id' => 'sac-form']
        ]);
    }
}
