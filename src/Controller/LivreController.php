<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LivreController extends AbstractController
{
    #[Route('/livre', name: 'livre.index')]
    public function index(Request $request, LivreRepository $repository): Response
    {
        $livres = $repository->findAll();
        return $this->render('livre/index.html.twig', [
            'livres' => $livres,
        ]);
    }

    #[Route('/livre/{slug}-{id}', name: 'livre.show', requirements: ['id' => '\d+', 'slug' => '[A-Za-z0-9-]+'])]
    public function show(Request $request, LivreRepository $repository, string $slug, int $id): Response
    {
        $livre = $repository->find($id);

        if(!$livre) {
            throw $this->createNotFoundException("Le livre avec l'id {$id} n'existe pas.");
        }

        if($livre->getSlug() !== $slug) {
            return $this->redirectToRoute('livre.show', ['slug' => $livre->getSlug(), 'id' => $livre->getId()]);
        }

        return $this->render('livre/index.html.twig', [
            'livre' => $livre,
        ]);
    }
}
