<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\Campus;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
          ->add('q', TextType::class, [
              'label' => false,
              'required' => false,
              'attr' => [
                  'placeholder' => 'Mot-clé'
              ]
          ])
          ->add('campus', EntityType::class, [
              'label'=> false,
              'required' => false,
              'class' => Campus::class,
              'choice_label' => 'nom'

          ])

          ->add('dateMin', DateType::class, [
              'label' => false,
              'required' => false,
              'html5'=> true,
              'widget'=>"single_text"
          ])
          ->add('dateMax', DateType::class, [
              'label' => false,
              'required' => false,
              'html5'=> true,
              'widget'=>"single_text"
          ])

          ->add('organisateur', CheckboxType::class, [
              'label'=> false,
              'required'=>false
          ])

          ->add('participant', CheckboxType::class, [
              'label'=> 'Sorties auxquelles je suis inscrit\(e\)',
              'required'=>false
          ])

          ->add('nonParticipant', CheckboxType::class, [
              'label'=> 'Sorties auxquelles je ne suis pas inscrit\(e\)',
              'required'=>false
          ])

          ->add('sortiesPassees', CheckboxType::class, [
              'label'=> 'Les sorties passées',
              'required'=>false
          ])

        ;

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                    'data_class' => SearchData::class,
                    'method' => 'GET',
                    'csrf_protection' => false
        ]);
    }

    //pour avoir une url la plus propre possible
    public function getBlockPrefix()
    {
        return '';
    }

}