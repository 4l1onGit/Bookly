<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $book = new Book();
            $book->setBookTitle($faker->sentence(3));
            $book->setAuthor($faker->name());
            $book->setGenre($faker->word());
            $book->setPages($faker->numberBetween(100, 1000));
            $book->setSummary($faker->paragraph());
            $book->setBookCover("https://picsum.photos/seed/book" . $i . "/200/300");


            $manager->persist($book);
        }

        $manager->flush();
    }
}