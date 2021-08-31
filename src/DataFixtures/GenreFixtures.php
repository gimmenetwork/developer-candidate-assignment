<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class GenreFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 20; $i++) {
            $genre = (new Genre())
                ->setName($faker->word)
            ;
            $this->setReference(sprintf('%s_%s', Genre::class, $i), $genre);
            $manager->persist($genre);
        }

        $manager->flush();
    }
}