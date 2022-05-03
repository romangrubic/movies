<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    /**
     * @Route("/movies", name="movies")
     */
    public function index(): Response
    {
        $movies = ["Avengers", "Inception", "Loki", "Sonic 2"];

        return $this->render('index.html.twig', array(
            "movies" => $movies
        ));
    }
}
