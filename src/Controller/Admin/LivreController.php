<?php

namespace App\Controller\Admin;

use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Livre;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('admin/livre', name: 'admin.livre.')]
final class LivreController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, LivreRepository $repository): Response
    {
        //$livres = $repository->findWithPublicationYearLowerThan(2023);
        //dd($livres);
        $livres = $repository->findAll();
        return $this->render('admin/livre/index.html.twig', [
            'livres' => $livres,
        ]);
    }

    #[Route('/{slug}-{id}', name: 'show', requirements: ['id' => '\d+', 'slug' => '[A-Za-z0-9-]+'])]
    public function show(Request $request, LivreRepository $repository, string $slug, int $id): Response
    {
        $livre = $repository->find($id);

        if(!$livre) {
            throw $this->createNotFoundException("Le livre avec l'id {$id} n'existe pas.");
        }

        if($livre->getSlug() !== $slug) {
            return $this->redirectToRoute('admin.livre.show', ['slug' => $livre->getSlug(), 'id' => $livre->getId()]);
        }

        return $this->render('admin/livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => requirement::DIGITS])]
    public function edit(Request $request, EntityManagerInterface $em, Livre $livre): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Le livre a bien été modifié');
            return $this->redirectToRoute('admin.livre.index');
        }

        return $this->render('admin/livre/edit.html.twig', [
            'livre' => $livre,
            'form' => $form
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($livre);
            $em->flush();

            $this->addFlash('success', 'Le livre a bien été crée');
            return $this->redirectToRoute('admin.livre.index');
        }

        return $this->render('admin/livre/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'])]
    public function remove(EntityManagerInterface $em, Livre $livre): Response
    {
        $em->remove($livre);
        $em->flush();
        $this->addFlash('success', 'Le livre a bien été supprimé');
        return $this->redirectToRoute('admin.livre.index');
    }

}
