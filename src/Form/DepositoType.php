<?php

namespace App\Form;

use App\Entity\Deposito;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepositoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero', null, [
                'attr' => ['autofocus' => true, 'class' => 'form-control form-control-border border-width-2 text-sm text-muted', 'autocomplete' => 'off', 'placeholder' => 'Número'],
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, array($this, 'submitData'));
    }

    /**
     * @param FormEvent $event
     */
    function submitData(FormEvent $event)
    {
        $deposito = $event->getData();

        if (null !== $depositoNumero = $deposito->getNumero()) {
            if (strlen($depositoNumero) == 1) {
                $depositoNumero = '0' . $depositoNumero;
            }

            $deposito->setNumero($depositoNumero);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Deposito::class,
            'attr' => ['id' => 'sac-form']
        ]);
    }
}
