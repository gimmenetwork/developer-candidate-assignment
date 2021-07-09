<?php

namespace App\DataFixtures;

use App\Entity\Reader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ReaderFixtures extends Fixture
{
    /**
     * @var Factory $faker
     */
    protected $faker;

    const READER_FIXTURE_REFERANCE = "reader_fixture";

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();

        for ($i = 0; $i < 20; $i++) {
            $record = new Reader();

            $record->setName($this->faker->firstName . ' ' . $this->faker->lastName);

            $manager->persist($record);

            $this->addReference(self::READER_FIXTURE_REFERANCE . '_' . $i, $record);
        }

        $manager->flush();
    }
}
