<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class OpenLibraryController extends  AbstractController
{
    private  $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route("/isbn/{id}", name: "book_isbn",  methods: "GET")]
    public function indexAction($id) {
        $client = new Client(['base_uri' => 'https://openlibrary.org/']);
        try {
            $res = $client->request('GET', 'api/volumes/brief/isbn/'.$id . '.json');
            $body = $res->getBody();
            $data = json_decode($body, true);
        } catch (GuzzleException $e) {
            return $this->json($e->getMessage());
        }
        $keys = [];
        foreach ($data['records'] as $key => $value) {
            $keys[] = $key;
        }

        return $this->render('pages/isbn/index.html.twig', ['book' => $data['records'][$keys[0]]['data']]);
    }
}