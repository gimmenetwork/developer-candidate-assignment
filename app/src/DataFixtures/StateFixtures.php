<?php

namespace App\DataFixtures;

use App\Entity\BookState;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class StateFixtures extends Fixture
{
    /**
     * @var Factory $faker
     */
    protected $faker;

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $record = new BookState();

            $book = $this->getReference(BookFixtures::BOOK_FIXTURE_REFERANCE . '_' . $this->faker->numberBetween(0, 49));
            $reader = $this->getReference(ReaderFixtures::READER_FIXTURE_REFERANCE . '_' . $this->faker->numberBetween(0, 19));

            $record->setBook($book);
            $record->setReader($reader);
            $record->setCreatedAt($this->faker->dateTimeBetween($startDate = '-2 months', $endDate = '-1 months', $timezone = null));

            $isReturned = rand(0,1);
            if($isReturned){
                $record->setReturnDate($this->faker->dateTimeBetween($startDate = '-1 months', $endDate = 'now', $timezone = null));
            }

            $manager->persist($record);

            if(!$isReturned){
                $book->setTaken($record);
                $manager->persist($book);
            }

        }

        $manager->flush();
    }
}
