<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, LivreRepository $rep): Response
    {
        //dd($request);
        //return new Response('Bonjour ' .$request->query->get('name'));
        //return new Response('Hello Dawan');

        $livres = $rep->findAll();
        //dd($livres);

        return $this->render('home/index.html.twig', [
            'livres' => $livres,
        ]);
    }
}
