<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookReview;
use App\Entity\Review;
use App\Form\BookReviewType;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route("/review", name: "index_review", methods: "GET")]
    public function index(Request $request): Response
    {
        $reviewRepo = $this->em->getRepository(Review::class);
        $reviews = $reviewRepo->findAll();

        return $this->render('pages/review/index_review.html.twig', ['reviews' => $reviews, 'user' => $this->getUser()]);
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
            return new Response('Form submitted', 200);
            // return $this->redirect('/',200);
        }
        return $this->render('pages/review/create_review.html.twig', ['form' => $form, 'user' => $this->getUser(), 'books' => $books]);
    }

    #[Route("/review/edit/{id}", name: "edit_review", methods: "PATCH")]
    public function editReview(Request $request, $id): Response
    {

        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        $bookRepo = $this->em->getRepository(Book::class);
        $books = $bookRepo->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $updatedReview = $form->getData();
            $this->em->persist($updatedReview);
            $this->em->flush();
            return new Response('Form submitted', 200);
            // return $this->redirect('/',200);
        }
        return $this->render('pages/review/edit_review.html.twig', ['form' => $form, 'user' => $this->getUser(), 'books' => $books]);
    }

    public function deleteReview(Request $request, $id): Response {}
}
