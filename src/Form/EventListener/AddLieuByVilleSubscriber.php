<?php

namespace App\Form\EventListener;

use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddLieuByVilleSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }

    public static function getSubscribedEvents(): array
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        // return [FormEvents::PRE_SET_DATA => 'preSetData'];
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        $lieu = $event->getData()->getLieu();

        $ville = $lieu ? $lieu->getVille() : null;

        $form = $event->getForm();
        $form = $this->addVilleEtLieuxForm($form, $ville);

        $form
            ->add('save', SubmitType::class, [
                'label' => 'Create Sortie',
            ])
            ->add('saveAndPublish', SubmitType::class, [
                'label' => 'Create et publier la Sortie',
            ])
        ;
    }

    public function preSubmit(FormEvent $event): void
    {
        $ville = $this->entityManagerInterface->getRepository(Ville::class)->find($event->getData()['ville']);

        $form = $event->getForm();

        $form = $this->addVilleEtLieuxForm($form, $ville);
    }

    private function addVilleEtLieuxForm(Form $form, ?Ville $ville): Form
    {
        $form
            ->add('ville', EntityType::class, [
                    'class' => Ville::class,
                    'choice_label' => 'nom',
                    'placeholder' => 'Selectione une Ville',
                    'mapped' => false,
                    'data' => $ville,
            ])
        ;
        $lieux = $ville ? $ville->getLieux() : [];
        $form
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'choices' => $lieux,
            ]);

        return $form;
    }
}