<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BookFixtures extends Fixture
{
    /**
     * @var Factory $faker
     */
    protected $faker;

    const GENRES = ["action","classics","comic","fantasy","fiction","horror"];

    const BOOK_FIXTURE_REFERANCE = "book_fixture";

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $record = new Book();

            $record->setName($this->faker->company);
            $record->setAuthor($this->faker->name);
            $record->setGenre(self::GENRES[rand(0,(count(self::GENRES)-1))]);

            $manager->persist($record);

            $this->addReference(self::BOOK_FIXTURE_REFERANCE . '_' . $i, $record);
        }

        $manager->flush();
    }
}
