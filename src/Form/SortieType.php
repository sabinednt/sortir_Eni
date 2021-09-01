<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Form\EventListener\AddLieuByVilleSubscriber;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    private $addLieuByVilleSubscriber;

    public function __construct(AddLieuByVilleSubscriber $addLieuByVilleSubscriber)
    {
        $this->addLieuByVilleSubscriber = $addLieuByVilleSubscriber;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        ini_set('memory_limit', '1024M');

        $builder
            ->add('nom')
            ->add('dateHeureDebut', DateTimeType::class, ['html5' => true, 'widget' => 'single_text'])
            ->add('duree')
            ->add('dateLimiteInscription', DateType::class, ['html5' => true, 'widget' => 'single_text'])
            ->add('nbInscriptionsMax')
            ->add('infosSortie', TextareaType::class)
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
            ]);

        $builder->addEventSubscriber($this->addLieuByVilleSubscriber);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}