<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Review;
use App\Form\BookAPIType;
use App\Form\ReviewAPIType;
use App\Repository\BookRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use FOS\RestBundle\Controller\Annotations as Rest;

class BooksAPIController extends AbstractFOSRestController
{
    private $em;
    private $validator;

    private $bookRepo;

    private $reviewRepo;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator, BookRepository $bookRepo, ReviewRepository $reviewRepo)
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->bookRepo = $bookRepo;
        $this->reviewRepo = $reviewRepo;
    }

    #[Rest\Get('api/v1/books', name: 'books_list')]
    public function getBooks() {
        try {

            $reviews = $this->bookRepo->findAll();
            $this->em->flush();
        } catch (Exception $e) {
            return $this->json(['err' => $e], 500);
        }


        $view = $this->view($reviews, 200);

        return $this->handleView($view);
    }

    #[Rest\Get('api/v1/books/{b_id}', name: 'books')]
    public function getBook($b_id) {
        try {

            $reviews = $this->bookRepo->find($b_id);
            $this->em->flush();
        } catch (Exception $e) {
            return $this->json(['err' => $e], 500);
        }


        $view = $this->view($reviews, 200);

        return $this->handleView($view);

    }

    #[Rest\Post('api/v1/books', name: 'books_create')]
    public function createBook(Request $request) {
        $book = new Book();

        $form = $this->createForm(BookAPIType::class, $book, array('method' => 'POST'));

        $form->submit(json_decode($request->getContent(), true));


        if ($form->isSubmitted() && $form->isValid()) {
            $newBook = $form->getData();
            $this->em->persist($newBook);
            $this->em->flush();

            $view = $this->view($newBook, 201,  ['Location' => $request->getSchemeAndHttpHost() . '/api/v1/books/' . $newBook->getId()]);
        } else if ($form->isSubmitted() && !$form->isValid()) {

            $view = $this->view($form, 400);
        } else {
            $view = $this->view($form, 500);
        }

        return $this->handleView($view);
    }

    #[Rest\Put('api/v1/books/{b_id}', name: 'books_update')]
    public function updateBook(Request $request, $b_id) {

        $book = $this->bookRepo->find($b_id);

        if ($book != null) {
            $form = $this->createForm(BookAPIType::class, $book, array('method' => 'PUT'));

            $form->submit(json_decode($request->getContent(), true));

            if ($form->isSubmitted() && $form->isValid()) {
                $newBook = $form->getData();
                $this->em->persist($newBook);
                $this->em->flush();

                $view = $this->view($book, 201,  ['Location' => $request->getSchemeAndHttpHost() . '/api/v1/books/' . $newBook->getId()]);
            } else if ($form->isSubmitted() && !$form->isValid()) {

                $view = $this->view($form, 400);
            } else {
                $view = $this->view($form, 400);
            }
        } else {
            $view = $this->view(['error' => 'Book not found'], 404);
        }

        return $this->handleView($view);
    }

    #[Rest\Delete('api/v1/books/{b_id}', name: 'books_delete')]
    public function deleteBook($b_id) {

        $book = $this->bookRepo->find($b_id);
        if ($book) {
            $this->em->remove($book);
            $view = $this->view(null, 204);
            $this->em->flush();
        } else {
            $view = $this->view(['message' => 'Book not found'], 404);
        }

        return $this->handleView($view);
    }

    #[Rest\Get('api/v1/books/{b_id}/reviews', name: 'books_reviews_list')]
    public function getReviews($b_id) {
        $book = $this->bookRepo->find($b_id);
        if($book == null) {
            $view = $this->view(['error' => 'Book reviews not found'], 404);
        } else {
            $view = $this->view($book->getReviews(), 200);

        }
        return $this->handleView($view);
    }

    #[Rest\Get('api/v1/books/{b_id}/reviews/{r_id}', name: 'books_reviews')]
    public function getReview($b_id, $r_id) {
        $book = $this->bookRepo->find($b_id);
        if($book == null) {
            $view = $this->view(['error' => 'Book reviews not found'], 404);
        } else {
            $review = $this->reviewRepo->findOneBy(['book' => $book, 'id' => $r_id]);

            if($review == null) {
                $view = $this->view(['error' => 'Review not found'], 404);
            } else {
                $view = $this->view($review, 200);
            }

        }

        return $this->handleView($view);
    }

    #[Rest\Post('api/v1/books/{b_id}/reviews', name: 'books_reviews_create')]
    public function createReview(Request $request, $b_id) {
        $review = new Review();


        $book = $this->bookRepo->find($b_id);
        if($book == null) {
            $view = $this->view(['error' => 'Book not found'], 404);
        } else {

            $form = $this->createForm(ReviewAPIType::class, $review, array('method' => 'POST'));
            $form->submit(json_decode($request->getContent(), true));
            if ($form->isSubmitted() && $form->isValid()) {
                $review->setReviewer($this->getUser());
                $review->setBook($book);
                $this->em->persist($review);
                $this->em->flush();
                $view = $this->view($review, 201);
            } else {
                $view = $this->view($form, 400);
            }
        }

        return $this->handleView($view);
}

    #[Rest\Put('api/v1/books/{b_id}/reviews/{r_id}', name: 'books_reviews_update')]
    public function updateReview(Request $request, $b_id, $r_id) {
        $book = $this->bookRepo->find($b_id);
        if($book == null) {
            $view = $this->view(['error' => 'Book reviews not found'], 404);
        } else {
            $review = $this->reviewRepo->findOneBy(['book' => $book, 'id' => $r_id]);
            if($review == null) {
                $view = $this->view(['error' => 'Review not found'], 404);
            } else {
                if($review->getReviewer() !== $this->getUser()) {
                    if($this->isGranted('ROLE_ADMIN')) {
                        $review->setReviewer($this->getUser());
                    } else if($this->isGranted('ROLE_MOD')) {
                        $review->setReviewer($this->getUser());
                    } else {
                        $view = $this->view(['error' => 'You can not edit this review'], 403);
                        return $this->handleView($view);
                    }

                }
            }
            $form = $this->createForm(ReviewAPIType::class, $review, array('method' => 'PUT'));
            $form->submit(json_decode($request->getContent(), true));
            if ($form->isSubmitted() && $form->isValid()) {
                $newReview = $form->getData();
                $newReview->setReviewer($this->getUser());
                $book->addReview($newReview);
                $this->em->persist($book);
                $this->em->flush();
                $view = $this->view($newReview, 200);
            } else {
                $view = $this->view($form, 400);
            }
        }


        return $this->handleView($view);

    }

    #[Rest\Delete('api/v1/books/{b_id}/reviews/{r_id}', name: 'books_reviews_delete')]
    public function deleteReview($b_id, $r_id) {
        $book = $this->bookRepo->find($b_id);

        if($book == null) {
            $view = $this->view(['error' => 'Book not found'], 404);
        } else {
            $review = $this->reviewRepo->findOneBy(['book' => $book, 'id' => $r_id]);

            if($review == null) {
                $view = $this->view(['error' => 'Review not found'], 404);
            } else {
                if($review->getReviewer() !== $this->getUser()) {
                    if($this->isGranted('ROLE_ADMIN')) {
                        $this->em->remove($review);
                        $this->em->flush();
                        $view = $this->view(null, 204);
                    } else {
                        $view = $this->view(['error' => 'You can not delete this review'], 403);
                    }
                } else {
                    $this->em->remove($review);
                    $this->em->flush();
                    $view = $this->view(null, 204);
                }

            }
        }


        return $this->handleView($view);
    }
}
