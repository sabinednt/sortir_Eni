<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $campusNom = [
            'SAINT HERBLAIN',
            'CHARTRES DE BRETAGNE',
            'LA ROCHE SUR YON',
        ];

        foreach ($campusNom as $key => $nom) {
            $campus = new Campus();
            $campus->setNom($nom);
            $manager->persist($campus);
            $manager->flush();

            $this->setReference('campus_'.$key, $campus);
        }
    }
}