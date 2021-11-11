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
                /** @var $estante */
                $estante = $event->getData();

                if (null !== $estanteNumero = $estante->getNumero()) {

                    if ( strlen($estanteNumero) == 1 ) {
                        $estanteNumero = '0'.$estanteNumero;
                    }

                    $estante->setNumero($estanteNumero);
                    $estante->setIdentificador('EST' . strtoupper(str_replace(' ', '', $estanteNumero)));
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
            'data_class' => Estante::class,
            'attr' => ['id' => 'sac-form']
        ]);
    }
}
