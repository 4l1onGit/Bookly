<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookAPIType;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GoogleBooksController extends  AbstractController
{

    private $googleBooksService;
    private $em;
    private $validator;


    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator) {
        $this->em = $em;
        $this->validator = $validator;
    }

    #[Route("/library", name: "list_library",  methods: "GET")]
    public function booksAction(Request $request) {
        $maxResults = $request->query->getInt('maxResults', 10);
        $start = $request->query->getInt('page', 0);
        $client = new Client(['base_uri' => 'https://www.googleapis.com/books/v1/']);
        try {
            $res = $client->request('GET', 'volumes', ['query' =>  ['q' => 'all', 'maxResults' => $maxResults, 'startIndex' => $start]]);
            $body = $res->getBody();
            $data = json_decode($body, true);

        } catch (GuzzleException $e) {
            return $this->redirectToRoute('list_library');
        }

        $totalPages = $data["totalItems"] / $maxResults;

        return $this->render('pages/library/index.html.twig', ['books' => $data["items"], 'totalPages' => $totalPages, 'page' => $start]);
    }

    #[Route("/library/create/{id}", name: "create_library")]
    public function addBookAction(Request $request, $id) {

        $client = new Client();

        try {
            $res = $client->request('GET', 'https://www.googleapis.com/books/v1/volumes/' . $id);
            $body = $res->getBody();
            $data = json_decode($body, true);


        } catch (GuzzleException $e) {
            return $this->json($e->getMessage(), 500);
        }

        try {
            $book = new Book();
            if(isset($data['volumeInfo']['title'])) {
                $book->setBookTitle($data['volumeInfo']['title']);
            }
            if(isset($data['volumeInfo']['authors'])) {
                $book->setAuthor($data['volumeInfo']['authors'][0]);
            }
            if(isset($data['volumeInfo']['categories'])) {
                $book->setGenre($data['volumeInfo']['categories'][0]);
            }
            if(isset($data['volumeInfo']['pageCount'])) {
                $book->setPages($data['volumeInfo']['pageCount']);
            }
            if(isset($data['volumeInfo']['imageLinks']["thumbnail"])) {
                $book->setBookCover($data['volumeInfo']['imageLinks']["thumbnail"]);
            }
            if(isset($data['volumeInfo']['description'])) {
                $book->setSummary(mb_strimwidth( $data['volumeInfo']['description'], 0, 255, "..."));
            }

        } catch (\Exception $e) {
            return $this->json($e->getMessage(), 500);
        }

        $form = $this->createForm(BookAPIType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($book);
            $this->em->flush();
            return $this->redirect($this->generateUrl('index_book'));
        } else {
            $errors = $this->validator->validate($form->getData());
            return $this->render('pages/library/add_library.html.twig', ['form' => $form, 'errors' => $errors]);
        }

    }



    #[Route("/library/author", name: "list_author_books",  methods: "GET")]
    public function searchAuthor(Request $request) {
        $client = new Client();
        $query = $request->query->getString('q', 'all');
        try {
            $res = $client->request('GET', 'https://www.googleapis.com/books/v1/volumes/', ['query' =>  ['q' => 'inauthor:' . $query]]);
            $body = $res->getBody();
            $data = json_decode($body, true);


        } catch (GuzzleException $e) {
            return $this->json($e->getMessage(), 500);
        }
        return $this->render('pages/library/view_author.html.twig', ['books' => $data["items"]]);
    }

}