<?php 
namespace App\Controller;

use App\Entity\BookReview;
use App\Form\BookReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends AbstractController 
{
    private $em;
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
      }
    /**
     * 
     * @Route("/review/create", name="create_review", methods={"POST"})
     * 
     */
    public function createReview(Request $request): Response 
    {

        $review = new BookReview();
        $form = $this->createForm(BookReviewType::class, $review);
        
        $form->handleRequest($request);
    
        if($form->isSubmitted() && $form->isValid()) {
            $newReview = $form->getData();
            $this->em->persist($newReview);
            $this->em->flush();
            return new Response('Form submitted', 200);
            // return $this->redirect('/',200);
        } 
        return $this->render('pages/create_review.html.twig', ['form' => $form]);
    }
}