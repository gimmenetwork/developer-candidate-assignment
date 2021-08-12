<?php

declare(strict_types=1);

namespace GimmeBook\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210809152245_dataset extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fills the DB with test dataset';
    }

    public function up(Schema $schema): void
    {
        $titles = [
            "Unlocking Android",
            "Android in Action, Second Edition",
            "Specification by Example",
            "Flex 3 in Action",
            "Flex 4 in Action",
            "Collective Intelligence in Action",
            "Zend Framework in Action",
            "Flex on Java",
            "Griffon in Action",
            "OSGi in Depth",
            "Flexible Rails",
            "Hello! Flex 4",
            "Coffeehouse",
            "Team Foundation Server 2008 in Action",
            "Brownfield Application Development in .NET",
            "MongoDB in Action",
            "Distributed Application Development with PowerBuilder 6.0",
            "Jaguar Development with PowerBuilder 7",
            "Taming Jaguar",
            "3D User Interfaces with Java 3D",
            "Hibernate in Action",
            "Hibernate in Action (Chinese Edition)",
            "Java Persistence with Hibernate",
            "JSTL in Action",
            "iBATIS in Action",
            "Designing Hard Software",
            "Hibernate Search in Action",
            "jQuery in Action",
            "jQuery in Action",
            "Building Secure and Reliable Network Applications",
            "Ruby for Rails",
            "The Well-Grounded Rubyist",
            "Website Owner's Manual",
            "ASP.NET 4.0 in Practice",
            "Hello! Python",
            "PFC Programmer's Reference Manual",
            "Graphics File Formats",
            "Visual Object Oriented Programming",
            "iOS in Practice",
            "iPhone in Action",
            "Silverlight 2 in Action",
            "The Quick Python Book, Second Edition",
            "Internet and Intranet Applications with PowerBuilder 6",
            "Practical Methods for Your Year 2000 Problem",
            "Mobile Agents",
            "Spring Dynamic Modules in Action",
            "SQL Server 2008 Administration in Action",
            "Android in Practice",
            "Object Oriented Perl",
        ];
        $bookSql = 'INSERT INTO book (title, year, count_in_stock, total_count) VALUES ';
        foreach ($titles as $title) {
            $year = random_int(1990, 2020);
            $inStock = random_int(0, 10);
            $total = $inStock + random_int(0, 10);
            $bookSql .= "(\"$title\", $year, $inStock, $total),";
        }

        // remove last comma
        $bookSql = substr($bookSql, 0, -1);
        $this->addSql($bookSql);

        // authors
        $authors = ['Johan Aakerlund','American Astronomical Society ','Ivar Aavatsmark','Omar Abdool','Noriyuki Abe','S. P. Abhinandan','Paul Abraham','Paul W. Abrahams','Per Abrahamsen','Adobe Systems Incorporated ','Hendri Adriaens','Luis Marco Adrián','Pasquale Claudio Africa','Giulio Agostini','Juan M. Aguirregabiria','Ezio Aimé','Tigran Aivazian','Mohammad M. Ajallooeian','Leila Akhmadeeva','Igor Akkerman','Vardan Akopian','Miguel Alabau','Abass B. Alamnehe','Robert Alessi','A. J. Alex','Jason Alexander','James Alexander','Bernard Alfonsi','Manuel Gutierrez Algaba','Raphaël Allais','Matthew Allen','Robert Allgeyer','Sean Allred','Dilum Aluthge','Morten Alver','Kamal Al-Yahya','Mohammed Obaid Alziyadi','Brian Amberg','American Meteorological Society ','Mahmood Amintoosi','The American Mathematical Society ','Paul C. Anagnostopoulos','Michael Anderson','Lenimar N. Andrade','Aleksandr Andreev','Avery D Andrews','Phil Andrews','Liviu Andronic','Ivan Andrus','Pablo Angulo'];
        $this->addSql(
            'INSERT INTO author (name) VALUES '
            . implode(',', array_map(
                static fn (string $name) => "(\"$name\")",
                $authors
            ))
        );

        // genres
        $genres = [
            'SQL',
            'Java',
            'Python',
            'JavaScript',
            'C#',
            'PHP',
            'Symfony',
            'RabbitMQ',
            'C++',
            'Electronics',
        ];
        $this->addSql(
            'INSERT INTO genre (name) VALUES '
            . implode(',', array_map(
                static fn (string $name) => "(\"$name\")",
                $genres
            ))
        );

        for ($i = 1, $iMax = count($titles); $i < $iMax; $i++) {
            $authorsRange = range(1, count($authors), random_int(2, count($authors) - 2));
            $genresRange = range(1, count($genres), random_int(2, count($genres) - 2));

            foreach ($authorsRange as $authorId) {
                $this->addSql("INSERT INTO book_to_author (book_id, author_id) VALUE ($i, $authorId)");
            }
            foreach ($genresRange as $genreId) {
                $this->addSql("INSERT INTO book_to_genre (book_id, genre_id) VALUE ($i, $genreId)");
            }
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('TRUNCATE TABLE book_to_author');
        $this->addSql('TRUNCATE TABLE book_to_genre');
        $this->addSql('TRUNCATE TABLE author');
        $this->addSql('TRUNCATE TABLE book');
        $this->addSql('TRUNCATE TABLE genre');
    }
}
