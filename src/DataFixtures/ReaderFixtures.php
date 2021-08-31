<?php

namespace App\DataFixtures;

use App\Entity\Reader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class ReaderFixtures
 *
 * @desc Create fixtures for Author entity
 * @package App\DataFixtures
 */
class ReaderFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $reader = (new Reader())->setName($faker->name);
            $this->setReference(sprintf('%s_%s', Reader::class, $i), $reader);

            $manager->persist($reader);
        }

        $manager->flush();
    }
}