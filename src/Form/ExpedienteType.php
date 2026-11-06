<?php

namespace App\Form;

use App\Entity\Anaquel;
use App\Entity\Deposito;
use App\Entity\Estante;
use App\Entity\Expediente;
use App\Entity\Fondo;
use App\Entity\Legajo;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpedienteType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero', null, [
                'attr' => ['autofocus' => true, 'class' => 'form-control form-control-border border-width-2 text-sm text-muted', 'autocomplete' => 'off', 'placeholder' => 'Número']
            ])
            ->add('contenido', null, [
                'attr' => ['class' => 'form-control form-control-border border-width-2 text-sm text-muted', 'autocomplete' => 'off', 'placeholder' => 'Contenido']
            ])
            ->add('estante', null, [
                'class' => Estante::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')->orderBy('e.numero', 'ASC');
                },
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('anaquel', null, [
                'class' => Anaquel::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')->orderBy('a.numero', 'ASC');
                },
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('legajo', null, [
                'class' => Legajo::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l')->orderBy('l.numero', 'ASC');
                },
                'attr' => ['class' => 'form-control'],
                'required' => false
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }

    /**
     * @param FormInterface $form
     * @param Fondo|null $fondo
     */
    protected function addElements(FormInterface $form, Fondo $fondo = null)
    {
        $form->add('fondo', null, [
            'attr' => ['class' => 'form-control'],
            'class' => Fondo::class,
            'required' => false,
            'data' => $fondo,
        ]);

        $depositosAsociados = array();

        if ($fondo) {
            $depositos = $this->entityManager->getRepository(Deposito::class);
            $depositosAsociados = $depositos->obtenerDepositosPorFondo($fondo->getId());
        }

        $form->add('deposito', null, [
            'attr' => ['class' => 'form-control'],
            'class' => Deposito::class,
            'choices' => $depositosAsociados,
        ]);
    }

    /**
     * @param FormEvent $event
     */
    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $fondo = $this->entityManager->getRepository(Fondo::class)->find($data['fondo']);

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
            'data_class' => Expediente::class,
            'attr' => ['id' => 'sac-form']
        ]);
    }
}
