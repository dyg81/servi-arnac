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
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero', null, [
                'attr' => ['autofocus' => true, 'class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'placeholder' => 'Identificador'],
                //'required' => false,
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var $anaquel */
                $anaquel = $event->getData();

                if (null !== $anaquelNumero = $anaquel->getNumero()) {

                    if (strlen($anaquelNumero) == 1) {
                        $anaquelNumero = '0' . $anaquelNumero;
                    }

                    $anaquel->setNumero($anaquelNumero);
                    $anaquel->setIdentificador('ANA_' . strtoupper(str_replace(' ', '', $anaquelNumero)));
                }
            });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Anaquel::class,
            'attr' => ['id' => 'sac-form']
        ]);
    }
}
