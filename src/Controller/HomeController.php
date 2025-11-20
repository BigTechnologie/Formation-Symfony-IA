<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\User;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, LivreRepository $rep, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        /*
        // Faux utilisateur
        $user = new User();
        $user->setEmail('admin@dawan.fr')
            ->setUsername('admin')
            ->setPassword($hasher->hashPassword($user, 'admin'))
            ->setRoles(["ROLE_ADMIN"]);
            $em->persist($user);
            $em->flush();
        */

        //dd($request);
        //return new Response('Bonjour ' .$request->query->get('name'));
        //return new Response('Hello Dawan');

        $livres = $rep->findAll();
        //dd($livres);

        return $this->render('home/index.html.twig', [
            'livres' => $livres,
        ]);
    }

    #[Route('/service')]
    public function service(ValidatorInterface $validator): Response
    {
       $livre = new Livre();
       $errors = $validator->validate($livre);
       dd((string)$errors);

    }
}
