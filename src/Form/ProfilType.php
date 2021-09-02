<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Doctrine\DBAL\Types\StringType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\File;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo')
            ->add('prenom')
            ->add('nom')
            ->add('email')
            ->add('telephone')
            ->add('motPasse', RepeatedType::class, [
            'mapped' => false,
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passe ne correspondent pas',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => false,
            'first_options'  => ['label' => 'Mot de passe : '],
            'second_options' => ['label' => 'Confirmation : '],
        ])
            ->add('fichier', FileType::class, [
                'data_class' => null,
                'label' => 'Photo de profil :',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Votre photo doit être en .jpeg, .jpg ou .png et ne pas dépasser 5 megaoctets'
                    ])
                ]
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
