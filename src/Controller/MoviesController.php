<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/movies", name="movies")
     */
    public function index(): Response
    {
        // findAll() -> Select * from movies;
        // find(5) - Select * from movies where id = 5;


        // findBy() = select * from movies ORDER by id DESC

        // $movies = $repository->findOneBy(['id' => 5, 'title' => 'The Dark Knight'],['id' => 'DESC']);
        // findOneBy = select * from movies where id = 5 and title = 'The Dark Knight' ORDER by DESC

        // $movies = $repository->count(['id' => 5]);
        // count() = select count() from movies where id = 5

        $repository = $this->em->getRepository(Movie::class);
        $movies = $repository->findByReleaseYear(2019);

        dd($movies);

        return $this->render('index.html.twig');
    }
}