<?php

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

    /**
     * @Route("/")
     */
    // ici on apelle EntityManagerInterface dans $em
    public function index(EntityManagerInterface $em): Response
    {
        // On demande quel repository correspond à la class "Pin" pour le stocker dans "$repo" puis 
        // soit "Entity(repositoryClass=PinRepository::class)" car délaré dans l'Entity "Pin"
        $repo = $em->getRepository(Pin::class);

        
        // Appel de la fonction de Repository "findAll" et stockage de la reponse dans $pins
        $pins = $repo->findall();

        return $this->render('pins/index.html.twig', [
            'pins' => $pins,
        ]
        // Envois de la reponse dans la page twig
    );
    }
REFACTORING:
    public function index(PinRepositoryface $repo): Response
    {
        return $this->render('pins/index.html.twig', ['pins' => $repo->findall()]);
    }