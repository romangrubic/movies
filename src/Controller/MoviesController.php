<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieFormType;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    private $movieRepository;
    private $em;

    public function __construct(MovieRepository $movieRepository, EntityManagerInterface $em)
    {
        $this->movieRepository = $movieRepository;
        $this->em = $em;
    }

    /**
     * @Route("/movies", methods={"GET"}, name="movies")
     */
    public function index(): Response
    {
        $movies = $this->movieRepository->findAll();

        return $this->render('movies/index.html.twig',[
            'movies' => $movies
        ]);
    }

    /**
     * @Route("/movies/create", name="create_movie")
     */
    public function create(Request $request): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);

        // POST method
        if ($form->isSubmitted() && $form->isValid()) {
            $newMovie = $form->getData();

            $imagePath = $form->get('imagePath')->getData();

            if($imagePath) {
                $newFileName = uniqid() . '.' .$imagePath->guessExtension();

                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $newMovie->setImagePath('/uploads/' . $newFileName);
            }

            // Saves it in database
            $this->em->persist($newMovie);
            $this->em->flush();

            return $this->redirectToRoute('movies');
        }

        return $this->render('movies/create.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/movies/edit/{id}", name="movie_edit")
     */
    public function edit($id, Request $request): Response
    {
        $movie = $this->movieRepository->find($id);
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);
        $imagePath = $form->get('imagePath')->getData();

        // If POST
        if ($form->isSubmitted() && $form->isValid()) {
            if ($imagePath) {
                if ($movie->getImagePath() !== null) {
                    if (file_exists($this->getParameter('kernel.project_dir') . '/public' . $movie->getImagePath())) {
                            $this->getParameter('kernel.project_dir') . $movie->getImagePath();

                            $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                            try {
                                $imagePath->move(
                                    $this->getParameter('kernel.project_dir').'/public/uploads',
                                    $newFileName
                                );
                            } catch (FileException $e) {
                                return new Response($e->getMessage());
                            }

                            $movie->setImagePath('/uploads/' . $newFileName);
                            $this->em->flush();

                            return $this->redirectToRoute('movies');
                        }
                }
            } else {
                $movie->setTitle($form->get('title')->getData());
                $movie->setReleaseYear($form->get('releaseYear')->getData());
                $movie->setDescription($form->get('description')->getData());

                $this->em->flush();
                return $this->redirectToRoute('movies');
            }
        }
        return $this->render('movies/edit.html.twig',[
            'movie' => $movie,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/movies/delete/{id}", methods={"GET", "DELETE"}, name="movie_delete")
     */
    public function delete($id): Response
    {
        $movie = $this->movieRepository->find($id);
        $this->em->remove($movie);
        $this->em->flush();
        return $this->redirectToRoute('movies');
    }

    /**
     * @Route("/movies/{id}", methods={"GET"}, name="movie_details")
     */
    public function show($id): Response
    {
        $movie = $this->movieRepository->find($id);

        return $this->render('movies/show.html.twig',[
            'movie' => $movie
        ]);
    }
}