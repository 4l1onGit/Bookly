<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormAPIType;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use FOS\RestBundle\Controller\Annotations as Rest;

class RegistrationAPIController extends AbstractFOSRestController
{
    #[Rest\Post('api/v1/register', name: 'user_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response  
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormAPIType::class, $user);
        
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

  

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('password')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();
                $view = $this->view(['message' => 'User registered successfully'], 201);
        } else {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }
            $view = $this->view(['errors' => $errors], 400);
        }


    

        return $this->handleView($view);
    }
}