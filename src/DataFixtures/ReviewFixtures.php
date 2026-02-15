<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 300; $i++) {
            $review = new Review();
            $review->setReviewText("Review content $i");
            $review->setRating(rand(1, 5));

    
            $user = $this->getReference("user_" . rand(1, 100), User::class);
              $book = $this->getReference("book_" . rand(1, 49), Book::class);

            $review->setReviewer($user);
            $review->setBook($book);

            $manager->persist($review);
        }

        $manager->flush();
    }

    // Ensure fixtures load in the correct order
    public function getDependencies(): array
    {
        return [
            BookFixtures::class,
            UserFixtures::class,
        ];
    }
}