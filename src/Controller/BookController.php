<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }



    #[Route("/", name: "index_book", methods: "GET")]
    public function home(): Response
    {
        $bookRepo = $this->em->getRepository(Book::class);
        $books = $bookRepo->findAll();
        $user = $this->getUser();

        return $this->render('pages/book/index.html.twig', ['books' => $books, 'user' => $user]);
    }

    #[Route("/book/{id}", name: "view_book", methods: "GET")]
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


    #[Route("/book/create", name: "create_book", methods: "POST")]
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
        return $this->render('pages/book/create_book.html.twig', ['form' => $form, 'user' => $this->getUser()]);
    }

    #[Route("/book/update/{id}", name: "update_book", methods: ['GET', 'PATCH'])]
    public function updateBook($id, Request $request): Response
    {
        $errors = [];
        $bookRepo = $this->em->getRepository(Book::class);
        $book = $bookRepo->find($id);
        $updatedBook = new Book();

        if ($book) {
            $form = $this->createForm(BookType::class, $updatedBook, ['method' => 'PATCH']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $formBookTitle = $form->get('book_title')->getData();
                $formAuthor = $form->get('author')->getData();
                $formGenre = $form->get('genre')->getData();
                $formPages = $form->get('pages')->getData();

                if ($book->getBookTitle() != $formBookTitle && $formBookTitle != null && $formBookTitle != '') {
                    $book->setBookTitle($formBookTitle);
                } else {
                    $error[] = "Title not valid, Title not updated";
                }

                if ($book->getAuthor() != $formAuthor && $formAuthor != null && $formAuthor != '') {
                    $book->setAuthor($formAuthor);
                } else {
                    $error[] = "Author not valid, Author not updated";
                }

                if ($book->getGenre() != $formGenre && $formGenre != null && $formGenre != '') {
                    $book->setGenre($formGenre);
                } else {
                    $error[] = "Author not valid, Author not updated";
                }

                if ($book->getPages() != $formPages && $formPages != null && $formPages != '') {
                    $book->setPages($formPages);
                } else {
                    $error[] = "Author not valid, Author not updated";
                }


                $this->em->persist($book);
                $this->em->flush();
            }
        } else {
            return new Response('Book not found', 404);
        }

        return $this->render('pages/book/update_book.html.twig', ['form' => $form, 'book' => $book, 'user' => $this->getUser()]);
    }


    #[Route("/book/delete/{id}", name: "delete_book")]
    public function deleteBook($id): Response
    {

        $bookRepo = $this->em->getRepository(Book::class);
        $book = $bookRepo->find($id);
        if ($book) {
            $this->em->remove($book);
        } else {
            return new Response('Book not found', 404);
        }

        $this->em->flush();

        return $this->redirect('/');
    }
}