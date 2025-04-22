<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\User;
use App\Form\ReviewAPIType;
use App\Form\ReviewType;
use App\Form\UserAPIType;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Cassandra\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use FOS\RestBundle\Controller\Annotations as Rest;

class UserAPIController extends AbstractFOSRestController
{
    private $em;
    private $validator;
    private $userRepo;
    private $reviewRepo;
    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator, UserRepository $userRepo, ReviewRepository $reviewRepo)
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->userRepo = $userRepo;
        $this->reviewRepo = $reviewRepo;
    }

    #[Rest\Get('api/v1/users', name: 'users_list')]
    public function getUsers() {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $users = $this->userRepo->findAll();
        $view = $this->view($users, 200);
        return $this->handleView($view);
    }

    #[Rest\Get('api/v1/users/{id}', name: 'users_single')]
    public function getAUser($id) {
        $user = $this->userRepo->find($id);
        if($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            $view = $this->view(['error' => 'You are not allowed to view this user'], 403);
        } else {
            $view = $this->view($user, 200);
        }

        return $this->handleView($view);
    }

    #[Rest\Post('api/v1/users', name: 'users_create')]
    public function createUser(Request $request, UserPasswordHasherInterface $userPasswordHasher) {
        $user = new User();
        $form = $this->createForm(UserAPIType::class, $user);
        $form->submit(json_decode($request->getContent(), true));
        if ($form->isSubmitted() && $form->isValid()) {
        $plainPassword = $form->get('password')->getData();

        $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
        $user->setRoles(['ROLE_USER']);
        $this->em->persist($user);
        $this->em->flush();
        $view = $this->view($user, 200);
        } else {
            $view = $this->view($form, 400);
        }
        return $this->handleView($view);
    }

    #[Rest\Put('api/v1/users/{id}', name: 'users_update')]
    public function updateUser($id, Request $request, UserPasswordHasherInterface $userPasswordHasher) {
        $user = $this->userRepo->find($id);
        $form = $this->createForm(UserAPIType::class, $user);
        $form->submit(json_decode($request->getContent(), true));
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();
            $view = $this->view($user, 200);
        } else {
            $view = $this->view($form, 400);
        }
        return $this->handleView($view);
    }

    #[Rest\Delete('api/v1/users/{id}', name: 'users_delete')]
    public function deleteUser($id) {
        $user = $this->userRepo->find($id);
        if($user === null) {
            $view = $this->view(['error' => 'User not found'], 404);
        } else {
            if($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
                $view = $this->view(['error' => 'You are not allowed to delete this user'], 403);
            } else {
                $this->em->remove($user);
                $this->em->flush();
                $view = $this->view(null, 200);
            }
        }

        return $this->handleView($view);
    }

    #[Rest\Get('api/v1/users/{id}/reviews', name: 'user_reviews')]
    public function getAUserReviews($id) {
        $user = $this->userRepo->find($id);
        if($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            $view = $this->view(['error' => 'You are not allowed to view this user'], 403);
        } else {
            $view = $this->view($user->getReviews(), 200);
        }
        return $this->handleView($view);
    }

    #[Rest\Get('api/v1/users/{u_id}/reviews/{r_id}', name: 'user_review')]
    public function getUserReview($u_id, $r_id) {
        $user = $this->userRepo->find($u_id);
        if($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            $view = $this->view(['error' => 'You are not allowed to view this user'], 403);
        } else {
            $reviews = $user->getReviews();
            $view = $this->view(['error' => 'Review not found'], 404);
            for($i = 0; $i < sizeof($user->getReviews()); $i++) {
                if($reviews[$i]->getId() == $r_id) {
                    $view = $this->view($reviews[$i], 200);
                }
            }

        }
        return $this->handleView($view);
    }

    #[Rest\Post('api/v1/users/reviews', name: 'user_create_review')]
    public function createUserReview(Request $request) {
        $review = new Review();
        $form = $this->createForm(ReviewAPIType::class, $review);
        $form->submit(json_decode($request->getContent(), true));
        if ($form->isSubmitted() && $form->isValid()) {
            $review->setReviewer($this->getUser());
            $this->em->persist($review);
            $this->em->flush();
            $view = $this->view($review, 200);
        } else {
            $view = $this->view($form, 400);
        }
        return $this->handleView($view);
    }

    #[Rest\Post('api/v1/users/{id}/reviews', name: 'user_create_for_user_review')]
    public function createReviewForUser($id, Request $request) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $review = new Review();
        $user = $this->userRepo->find($id);
        if($user !== null) {
            $form = $this->createForm(ReviewAPIType::class, $review);
            $form->submit(json_decode($request->getContent(), true));
            if ($form->isSubmitted() && $form->isValid()) {
                $review->setReviewer($user);
                $this->em->persist($review);
                $this->em->flush();
                $view = $this->view($review, 200);
            }  else {
                $view = $this->view($form, 400);
            }
        } else {
            $view = $this->view(['error' => 'user not found'], 404);
        }
        return $this->handleView($view);
    }


    #[Rest\Delete('api/v1/users/reviews/{id}', name: 'user_delete_review')]
    public function deleteUserReview($id, Request $request) {
        $review = $this->reviewRepo->find($id);
        if($review === null) {
            $view = $this->view(['error' => 'Review not found'], 404);
        } else {
           $this->em->remove($review);
           $this->em->flush();
           $view = $this->view(null, 200);
        }

        return $this->handleView($view);
    }

    #[Rest\Delete('api/v1/users/{u_id}/reviews/{r_id}', name: 'user_delete_for_user_review')]
    public function deleteAUserReview($u_id ,$r_id, Request $request) {
        $user = $this->userRepo->find($u_id);
        if($user === null) {
            $view = $this->view(['error' => 'User not found'], 404);
        } else {
            if($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
                $view = $this->view(['error' => 'You are not allowed to delete this users review'], 403);
            } else {
                $review = $this->reviewRepo->find($r_id);
                if($review === null) {
                    $view = $this->view(['error' => 'Review not found'], 404);
                } else {
                    $this->em->remove($review);
                    $this->em->flush();
                    $view = $this->view(null, 200);
                }
            }

        }
        return $this->handleView($view);
    }


}
