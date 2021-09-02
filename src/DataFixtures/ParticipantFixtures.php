<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ParticipantFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * encoder.
     *
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // Recupere les campus avec getReferences
        $campus = $this->getReference('campus_0');

        $actif = true;

        // Create Admin Participant

        $participantAdmin = new Participant();

        $participantAdmin->setActif($actif)
                    ->setAdministrateur(true)
                    ->setCampus($campus)
                    ->setEmail('admin@sortir.com')
                    ->setFichier($faker->imageUrl())
                    ->setNom($faker->lastName())
                    ->setPrenom($faker->firstName())
                    ->setPseudo('Admin')
                    ->setTelephone($faker->phoneNumber())
                    ;
        $password = $this->encoder->encodePassword($participantAdmin, 'passwordAdmin');
        $participantAdmin->setMotPasse($password);

        $manager->persist($participantAdmin);

        $numeroCampus = 0;

        // Create all Participant
        for ($i = 0; $i < 49; ++$i) {
            $participant = new Participant();
            // Recupere les campus avec getReferences
            $campus = $this->getReference('campus_'.$numeroCampus);
            if (2 === $numeroCampus) {
                $numeroCampus = 0;
            } else {
                ++$numeroCampus;
            }

            $nom = $faker->lastName();
            $prenom = $faker->firstName();
            if (0 === $i % 2) {
                $actif = false;
            } else {
                $actif = true;
            }
            $participant->setActif($actif)
                    ->setAdministrateur(false)
                    ->setCampus($campus)
                    ->setEmail($faker->email())
                    ->setFichier($faker->imageUrl())
                    ->setNom($nom)
                    ->setPrenom($prenom)
                    ->setPseudo($nom.$prenom)
                    ->setTelephone($faker->phoneNumber())
                    ;
            $password = $this->encoder->encodePassword($participant, 'password'.$i);
            $participant->setMotPasse($password);

            $manager->persist($participant);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CampusFixtures::class,
        ];
    }
}