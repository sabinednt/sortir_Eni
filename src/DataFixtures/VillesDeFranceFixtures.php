<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class VillesDeFranceFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $json = file_get_contents(__DIR__.'/villes/france.json');

        $villes = json_decode($json, true);

        $faker = Factory::create('fr_FR');

        foreach ($villes as $dataVille) {
            $ville = new Ville();

            $ville->setNom($dataVille['Nom_commune']);
            $ville->setCodePostal($dataVille['Code_postal']);

            for ($i = 0; $i < 2; ++$i) {
                $lieu = new Lieu();

                $lieu->setNom($faker->company());
                $lieu->setRue($faker->address());
                $lieu->setLatitude($faker->latitude());
                $lieu->setLongitude($faker->longitude());

                $lieu->setVille($ville);
                $ville->addLieux($lieu);
            }

            $manager->persist($lieu);
            $manager->persist($ville);
        }

        $manager->flush();
    }
}