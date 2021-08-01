<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Genre;
use App\Entity\Reader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * @throws \JsonException
     */
    public function load(ObjectManager $manager)
    {
        $data = [
            [
                "isbn" => 9789000307975,
                "title" => "Vrienden voor het leven",
                "author" => "Maeve Binchy",
                "summary" => "Vrienden voor het leven is het verhaal van drie vriendinnen die op weg naar volwassenheid verwikkeld raken in een zonderlinge driehoeksverhouding. Benny en Eve, boezemvriendinnen uit het Ierse dorpje Knockglen, gaan in Dublin studeren en sluiten daar al snel vriendschap met de aantrekkelijke en ambitieuze Nan. Het opwindende studentenleven brengt hun echter niet alleen geluk maar ook verdriet. Met haar grote vermogen om menselijke gevoelens herkenbaar neer te zetten weet Maeve Binchy geluk en verdriet, warmte en humor samen te brengen in deze meeslepende roman. Vrienden voor het leven verscheen voor het eerst in 1991 en is het favoriete boek van vele Maeve Binchy-fans. Het boek is inmiddels toe aan de zeventiende druk. In 1995 werd het zeer succesvol verfilmd onder de titel Circle of Friends met Minnie Driver en Chris O’Donnell in de hoofdrollen.",
                'genre' => 'Friendship',
            ],
            [
                "isbn" => 9780552159722,
                "title" => "Deception point",
                "author" => "Dan Brown",
                "summary" => "When a new NASA satellite detects evidence of an astonishingly rare object buried deep in the Arctic ice, the floundering space agency proclaims a much-needed victory.. a victory that has profound implications for U.S. space policy and the impending presidential election. With the Oval Office in the balance, the President dispatches White House Intelligence analyst Rachel Sexton to the Arctic to verify the authenticity of the find. Accompanied by a team of experts, including the charismatic academic Michael Tolland, Rachel uncovers the unthinkable - evidence of scientific trickery - a bold deception that threatens to plunge the world into controversy..",
                'genre' => 'Thriller',
            ],
            [
                "isbn" => 9789022558027,
                "title" => "Magic staff",
                "author" => "Terry Brooks",
                "summary" => "Vijf eeuwen geleden werd de wereld door een noodlottige demonenoorlog in de as gelegd. De overlevenden hebben een toevluchtsoord gevonden in een door magie beschermde vallei, maar nu staat een genadeloos leger op het punt de vallei binnen te vallen. De enige hoop op redding voor de overlevenden was Sider Ament, maar hij leeft niet meer. Sider was de drager van de enig overgebleven zwarte staf, een machtige talisman die eeuwenlang door de Ridders van het Woord is doorgegeven en die van cruciaal belang is bij het in evenwicht houden van de magie op de wereld. Om de wereld van de ondergang te redden, moet de magie van de staf behouden blijven. Panterra Qu, een jonge Spoorzoeker aan wie de staf na Siders dood wordt doorgegeven, heeft grote moeite om de macht ervan naar zijn hand te zetten. Alles moet op alles worden gezet, want eenieder zal een hoge tol betalen als de oorlog tussen het Woord en de Leegte naar de duisternis dreigt af te glijden. ",
                'genre' => 'Fantasy Fiction, High fantasy',
            ]
        ];

        foreach ($data as $item) {
            $author = new Author();
            $author->setName($item['author']);
            $manager->persist($author);

            $genre = new Genre();
            $genre->setName($item['genre']);
            $manager->persist($genre);

            $book = new Book();
            $book->setName($item['title']);
            $book->setIsbn($item['isbn']);
            $book->setSummary($item['summary']);
            $book->setAuthor($author);
            $book->setGenre($genre);
            $manager->persist($book);
        }

        $readers = ['Reader 1', 'Reader 2', 'Reader 3', 'Reader 4'];

        foreach ($readers as $item) {
            $reader =  new Reader();
            $reader->setName($item);

            $manager->persist($reader);
        }

        $manager->flush();
    }
}
