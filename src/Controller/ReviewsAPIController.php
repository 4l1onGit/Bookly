<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Review;
use App\Entity\User;
use App\Form\ReviewAPIType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

class ReviewsAPIController extends AbstractFOSRestController
{

    private $em;
    private $validator;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    // READ

    #[Rest\Get('/api/v1/reviews', name: 'reviews_list')]
    public function getReviews(Request $request): Response
    {

        $page = max(1, (int) $request->query->get('page', 1)); 
        $limit = max(1, (int) $request->query->get('limit', 10));
        try {
            $reviewsRepo = $this->em->getRepository(Review::class);
            $reviews = $reviewsRepo->findByPage(($page - 1) * $limit, $limit);
      
        } catch (Exception $e) {
            return $this->json(['err' => $e->getMessage()], 500);
        }


        $view = $this->view($reviews, 200);

        return $this->handleView($view);
    }


    #[Rest\Get('/api/v1/reviews/{id}', name: 'reviews')]
    public function getReview($id): Response
    {
        try {
            $reviewsRepo = $this->em->getRepository(Review::class);
            $reviews = $reviewsRepo->findById($id);
            $this->em->flush();
        } catch (Exception $e) {
            return $this->json(['err' => $e], 500);
        }
       
        $view = $this->view($reviews, 200);

        return $this->handleView($view);
    }

    // CREATE

    #[Rest\Post('/api/v1/reviews', name: 'reviews_create')]
    public function createReview(Request $request): Response
    {
        $review = new Review();
        $view = null;

        $form = $this->createForm(ReviewAPIType::class, $review, array('method' => 'POST'));

        $form->submit(json_decode($request->getContent(), true));


        if ($form->isSubmitted() && $form->isValid()) {
            $review->setReviewer($this->getUser());
            $this->em->persist($review);
            $this->em->flush();

            $view = $this->view($review, 201,  ['Location' => $request->getSchemeAndHttpHost() . '/api/v1/reviews/' . $review->getId()]);
        } else if ($form->isSubmitted() && !$form->isValid()) {

            $view = $this->view($form, 400);
        } else {
            $view = $this->view($form, 500);
        }

        return $this->handleView($view);
    }


    // UPDATE

    #[Rest\Put('/api/v1/reviews/{id}', name: 'reviews_update')]
    public function updateReview($id, Request $request): Response
    {


        $reviewRepo = $this->em->getRepository(Review::class);
        $review = $reviewRepo->find($id);
        $reviewUser = $review->getReviewer();

        if($reviewUser !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            $view = $this->view(['error' => 'You can only update your own reviews'], 403);
            return $this->handleView($view);
        }

        if ($review != null) {
            $form = $this->createForm(ReviewAPIType::class, $review, array('method' => 'PUT'));

            $form->submit(json_decode($request->getContent(), true));

            if ($form->isSubmitted() && $form->isValid()) {
                $review->setReviewer($reviewUser);
                $this->em->persist($review);
                $this->em->flush();

                $view = $this->view($review, 201,  ['Location' => $request->getSchemeAndHttpHost() . '/api/v1/reviews/' . $review->getId()]);
            } else if ($form->isSubmitted() && !$form->isValid()) {

                $view = $this->view($form, 400);
            } else {
                $view = $this->view($form, 400);
            }
        } else {
            $view = $this->view(['error' => 'Review not found'], 404);
        }

        return $this->handleView($view);
    }


    #[Rest\Delete('/api/v1/reviews/{id}', name: 'reviews_delete')]
    public function deleteReview($id,  Request $request): Response
    {

        $reviewRepo = $this->em->getRepository(Review::class);
        $review = $reviewRepo->find($id);
        $reviewUser = $review->getReviewer();
        if($reviewUser !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            $view = $this->view(['error' => 'You can only delete your own reviews'], 403);
            return $this->handleView($view);
        }

         if ($review == null) {
            $view = $this->view(['error' => 'Review not found'], 404);
        } else {
            $this->em->remove($review);
            $this->em->flush();
            $view = $this->view(null, 204);
        }

        if($review == null) {
            $view = $this->view(['error' => 'Review not found'], 404);
            return $this->handleView($view);
        }
        if ($review) {
            $this->em->remove($review);
            $view = $this->view( null, 204);
            $this->em->flush();
        } else {
            $view = $this->view(['message' => 'Review not found'], 404);
        }


        return $this->handleView($view);
    }
}