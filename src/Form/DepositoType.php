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
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero', null, [
                'attr'     => ['autofocus' => true, 'class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'placeholder' => 'Identificador'],
                //'required' => false,
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var $deposito */
                $deposito = $event->getData();

                if (null !== $depositoNumero = $deposito->getNumero()) {

                    if ( strlen($depositoNumero) == 1 ) {
                        $depositoNumero = '0'.$depositoNumero;
                    }

                    $deposito->setNumero($depositoNumero);
                    $deposito->setIdentificador('DNAVE' . strtoupper(str_replace(' ', '', $depositoNumero)));
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
            'data_class' => Deposito::class,
            'attr' => ['id' => 'sac-form']
        ]);
    }
}
