<?php

namespace App\Controller;

use App\Entity\Book;

use App\Entity\Review;

use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReviewController extends AbstractController
{
    private $em;
    private $validator;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    #[Route("/review", name: "index_review", methods: "GET")]
    public function index(): Response
    {
        $reviewRepo = $this->em->getRepository(Review::class);
        $reviews = $reviewRepo->findAll();

        return $this->render('pages/review/index_review.html.twig', ['reviews' => $reviews]);
    }

    #[Route("/review/create", name: "create_review", methods: "POST")]
    public function createReview(Request $request): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        $bookRepo = $this->em->getRepository(Book::class);
        $books = $bookRepo->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $newReview = $form->getData();
            $this->em->persist($newReview);
            $this->em->flush();

            return $this->redirect($this->generateUrl('index_book'));
        } else if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $this->validator->validate($form->getData());
            return $this->render('pages/review/create_review.html.twig', ['form' => $form, 'books' => $books, 'errors' => $errors, 'user' => $this->getUser()]);
        }
        return $this->render('pages/review/create_review.html.twig', ['form' => $form, 'books' => $books, 'user' => $this->getUser()]);
    }

    #[Route("/review/update/{id}", name: "update_review")]
    public function updateReview(Request $request, $id): Response
    {

        $reviewRepo = $this->em->getRepository(Review::class);
        $review = $reviewRepo->find($id);

        if ($review != null) {
            $form = $this->createForm(ReviewType::class, $review, ['method' => 'PATCH']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $formBook = $form->get('book')->getData();
                $formReviewer = $form->get('reviewer')->getData();
                $formRating = $form->get('rating')->getData();
                $formReviewText = $form->get('review_text')->getData();

                if ($review->getBook() != $formBook && $formBook != null && $formBook != '') {
                    $review->setBook($formBook);
                }
                if ($review->getReviewer() != $formReviewer && $formReviewer != null && $formReviewer != '') {
                    $review->setBook($formReviewer);
                }
                if ($review->getRating() != $formRating && $formRating != null && $formRating != '') {
                    $review->setBook($formRating);
                }
                if ($review->getReviewText() != $formReviewText && $formReviewText != null && $formReviewText != '') {
                    $review->setBook($formReviewText);
                }

                $this->em->persist($review);
                $this->em->flush();

                return $this->redirect($this->generateUrl('index_book'));
            }
        }

        $bookRepo = $this->em->getRepository(Book::class);
        $books = $bookRepo->findAll();

        return $this->render('pages/review/update_review.html.twig', ['form' => $form, 'user' => $this->getUser(), 'books' => $books]);
    }

    #[Route("/review/delete/{id}", name: "delete_review")]
    public function deleteReview($id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $reviewRepo = $this->em->getRepository(Review::class);
        $review = $reviewRepo->find($id);
        if ($review) {
            $this->em->remove($review);
        } else {
            return new Response('Review not found', 404);
        }

        $this->em->flush();

        return $this->redirect($this->generateUrl('index_book'));
    }
}
