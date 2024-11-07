<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookReview;
use App\Form\BookReviewType;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     *  
     * @Route("/", name="index_book", methods={"GET"})
     * 
     */
    public function home(): Response
    {
        $bookRepo = $this->em->getRepository(Book::class);
        $books = $bookRepo->findAll();

        return $this->render('pages/book/index.html.twig', ['books' => $books]);
    }

    /**
     *  
     * @Route("/book/{id}", name="index_book", methods={"GET"})
     * 
     */
    public function viewBook($id): Response
    {
        $bookRepo = $this->em->getRepository(Book::class);
        $book = $bookRepo->find($id);
        $user = $this->getUser();

        if ($book) {
            return $this->render('pages/book/view_book.html.twig', ['book' => $book, 'user' => $user]);
        } else {
            return new Response('Error book does not exist!', 404);
        }
    }

    /**
     * 
     * @Route("/book/create", name="create_book", methods={"POST"})
     * 
     */
    public function createBook(Request $request): Response
    {

        $book = new Book();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newBook = $form->getData();
            $bookImage = $form->get('bookCover')->getData();

            if ($bookImage) {

                $fileExt = $bookImage->guessExtension();
                $newFilename = uniqid() . '.' . $fileExt;


                try {
                    $bookImage->move($this->getParameter('kernel.project_dir') . '/public/books', $newFilename);
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }
                $newBook->setBookCover('/books/' . $newFilename);
            }


            $this->em->persist($newBook);
            $this->em->flush();

            return $this->redirect('/');
        }
        return $this->render('pages/book/create_book.html.twig', ['form' => $form]);
    }
}
