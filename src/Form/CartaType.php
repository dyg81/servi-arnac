<?php

namespace App\Form;

use App\Entity\Carta;
use App\Entity\Cliente;
use App\Entity\Fondo;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartaType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cliente', null, [
                'query_builder' => $this->obtenerClientesSinCartaSolicitada(),
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('documento_file', FileType::class, [
                'attr' => ['class' => 'form-control form-control-border border-width-2 text-sm text-muted'],
                'mapped' => false,
                'required' => false
            ])
            ->add('observaciones', null, [
                'attr' => ['class' => 'form-control form-control-border border-width-2 text-sm text-muted', 'autocomplete' => 'off', 'placeholder' => 'Observaciones']
            ]);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
    }

    /**
     * @return QueryBuilder
     */
    public function obtenerClientesSinCartaSolicitada(): QueryBuilder
    {
        $entityRepository = $this->entityManager->getRepository(Cliente::class);

        $subQuery = $entityRepository->createQueryBuilder('c')
            ->from(Carta::class, 'carta')
            ->where('carta.cliente = cliente')
            ->andWhere('carta.estado = :carta_solicitada');

        $query = $entityRepository
            ->createQueryBuilder('cliente')
            ->orderBy('cliente.nombre', 'ASC');

        $query
            ->andWhere($query->expr()->not($query->expr()->exists($subQuery->getDQL())))
            ->setParameter('carta_solicitada', Carta::CARTA_SOLICITADA);

        return $query;
    }

    /**
     * @param FormInterface $form
     * @param null $fondos
     */
    protected function addElements(FormInterface $form, $fondos = null)
    {
        $form->add('fondos', null, [
            'attr' => ['class' => 'form-control'],
            'class' => Fondo::class,
            'required' => false,
            'choices' => $fondos,
        ]);
    }

    /**
     * @param FormEvent $event
     */
    function onPresetData(FormEvent $event)
    {
        $carta = $event->getData();
        $form = $event->getForm();

        $fondosArray = array();

        if ($carta->getId()) {
            $fondos = $this->entityManager->getRepository(Fondo::class)->findAll();

            if ($cartas = $this->entityManager->getRepository(Carta::class)->obtenerCartasActivasDelCliente($carta->getCliente()->getId())) {
                foreach ($cartas as $c) {
                    $f = $this->entityManager->getRepository(Carta::class)->obtenerFondosPorCartaYCliente($c, $carta->getCliente()->getId());

                    foreach ($f as $fondo) {
                            $fondosArray[] = $fondo;
                    }
                }
                $fondosArray = array_diff($fondos, $fondosArray);
            } else {
                $fondosArray = $fondos;
            }
        }

        $this->addElements($form, $fondosArray);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Carta::class,
            'attr' => ['id' => 'sac-form']
        ]);
    }
}
