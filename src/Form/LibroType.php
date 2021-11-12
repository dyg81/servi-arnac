<?php

namespace App\Form;

use App\Entity\Anaquel;
use App\Entity\Deposito;
use App\Entity\Estante;
use App\Entity\Fondo;
use App\Entity\Libro;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LibroType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tomo', null, [
                'attr'     => ['autofocus' => true, 'class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'placeholder' => 'Tomo']
            ])
            ->add('anno', ChoiceType::class, [
                'choices'   => $this->getYears(1492),
                'attr'      => ['class' => 'form-control'],
                'required'  => false
            ])
            ->add('descripcion', null, [
                'attr'      => ['class' => 'form-control form-control-border border-width-2', 'autocomplete' => 'off', 'placeholder' => 'Descripción']
            ])
            ->add('estante', EntityType::class, [
                'class'         => Estante::class,
                'choice_label'  => 'identificador',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.numero', 'ASC');
                },
                'attr'          => ['class' => 'form-control'],
                'required'      => false
            ])
            ->add('anaquel', EntityType::class, [
                'class'         => Anaquel::class,
                'choice_label'  => 'identificador',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->orderBy('a.numero', 'ASC');
                },
                'attr'          => ['class' => 'form-control'],
                'required'      => false
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var $libro */
                $libro = $event->getData();

                if (null !== $libro->getTomo()) {
                    $libro->setIdentificador($libro->getTomo().'-'.$libro->getAnno().'-'.$libro->getFondo()->getIdentificador());
                }
            })
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }

    private function getYears($min, $max='current')
    {
        $years = range($min, ($max === 'current' ? date('Y') : $max));

        return array_combine($years, $years);
    }

    /**
     * @param FormInterface $form
     * @param Fondo|null $fondo
     */
    protected function addElements(FormInterface $form, Fondo $fondo = null)
    {
        $form->add('fondo', null, [
            'attr'     => ['class' => 'form-control'],
            'class'    => 'App\Entity\Fondo',
            'required' => false,
            'data'     => $fondo,
        ]);

        $depositosAsociados = array();

        if ($fondo) {
            $depositos = $this->em->getRepository(Deposito::class);
            $depositosAsociados = $depositos->findAllByFondo($fondo->getId());
        }

        $form->add('deposito', null, [
            'attr'     => ['class' => 'form-control'],
            'class'    => 'App\Entity\Deposito',
            'choices'  => $depositosAsociados,
        ]);
    }

    /**
     * @param FormEvent $event
     */
    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $fondo = $this->em->getRepository('App:Fondo')->find($data['fondo']);

        $this->addElements($form, $fondo);
    }

    /**
     * @param FormEvent $event
     */
    function onPresetData(FormEvent $event)
    {
        $expediente = $event->getData();
        $form = $event->getForm();

        $fondo = $expediente->getFondo() ? $expediente->getFondo() : null;

        $this->addElements($form, $fondo);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Libro::class,
            'attr' => ['id' => 'sac-form']
        ]);
    }
}
