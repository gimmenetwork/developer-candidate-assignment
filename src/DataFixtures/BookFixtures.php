<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Genre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BookFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 20; $i++) {
            $book = (new Book())
                ->setName($faker->sentence(6))
                ->setAuthor($faker->name)
                ->setGenre($this->getReference(sprintf('%s_%s', Genre::class, rand(1, 20))))
            ;

            $manager->persist($book);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
          GenreFixtures::class,
        ];
    }
}