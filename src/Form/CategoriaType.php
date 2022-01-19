<?php

namespace App\Form;

use App\Entity\Categoria;
use AppBundle\Library\Urlizer\Urlizer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoriaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', null, [
                'attr'     => ['autofocus' => true, 'class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'placeholder' => 'Nombre'],
                //'required' => false,
            ])
            ->add('transcripcion_precio', null, [
                'attr'     => ['class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'placeholder' => 'Transcripción'],
                //'required' => false,
            ])
            ->add('reprografia_normal_precio', null, [
                'attr'     => ['class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'placeholder' => 'Rep. tamaño normal'],
                //'required' => false,
            ])
            ->add('reprografia_grande_precio', null, [
                'attr'     => ['class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'placeholder' => 'Rep. gran formato'],
                //'required' => false,
            ])
            ->add('certificacion_precio', null, [
                'attr'     => ['class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'placeholder' => 'Certificación'],
                //'required' => false,
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var $categoria */
                $categoria = $event->getData();
                if (null !== $categoriaNombre = $categoria->getNombre()) {
                    $categoria->setIdentificador(strtolower(Urlizer::urlize($categoriaNombre)));
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categoria::class,
            'attr' => ['id' => 'sac-form']
        ]);
    }
}
