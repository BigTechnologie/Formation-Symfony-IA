<?php

namespace App\Controller\Admin;

use App\Form\LivreType;
use App\Repository\LivreRepository;
//use App\Security\Voter\LivreVoter;
use App\Security\Voter\GenericVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Livre;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire; // Import rajouté pour la ligne 28

#[Route('admin/livre', name: 'admin.livre.')]
//#[IsGranted('ROLE_ADMIN')]
final class LivreController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'cache.livre_pool')] private TagAwareCacheInterface $cache
        // J'ai modifié la ligne 28 pour demander à Symfony de m'injecter le service nommé cache.livre_pool défini dans le container Symfonyet.
        // Ce service correspond à un pool de cache tagué, défini dans notre cas dans config/packages/cache.yaml.
    )
    {
        $this->cache = $cache;
    }

     
    // Cas 2
    #[Route('/', name: 'index')]
    public function index(Request $request, LivreRepository $repository, Security $security): Response
    {
        $user = $security->getUser();
        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        $page = $request->query->getInt('page', 1);
        $canListAll = $security->isGranted(GenericVoter::LIST_ALL);
        $userId = $canListAll ? 'all' : $user->getId();

        $cacheKey = sprintf('livres_list_%s_page_%d', $userId, $page);

        $livres = $this->cache->get($cacheKey, function(ItemInterface $item) use ($repository, $page, $canListAll, $user) {
            $item->expiresAfter(900); // 15 mn

            $item->tag(tags: 'livres_list');

            return $repository->paginatelivres($page, $canListAll ? null : $user->getId());
        } );

        return $this->render('admin/livre/index.html.twig', [
            'livres' => $livres,
        ]);
    }


    /*
    // Cas 1
    #[Route('/', name: 'index')]
    public function index(Request $request, LivreRepository $repository, Security $security, CacheInterface $cache): Response
    {
        $user = $security->getUser();
        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        $page = $request->query->getInt('page', 1);
        $canListAll = $security->isGranted(GenericVoter::LIST_ALL);
        $userId = $canListAll ? 'all' : $user->getId();

        $cacheKey = sprintf('livres_list_%s_page_%d', $userId, $page);

        $livres = $cache->get($cacheKey, function(ItemInterface $item) use ($repository, $page, $canListAll, $user) {
            $item->expiresAfter(900); // 15 mn

            return $repository->paginatelivres($page, $canListAll ? null : $user->getId());
        } );

        return $this->render('admin/livre/index.html.twig', [
            'livres' => $livres,
        ]);
    }
    */
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

        $response = $this->render('admin/livre/show.html.twig', [
            'livre' => $livre,
        ]);

        $etag = md5($livre->getId() . $livre->getUpdatedAt()->getTimestamp());

        $response->setEtag($etag);

        $response->isNotModified($request);

        $response->setPublic();

        $response->setMaxAge(300);

        $response->setSharedMaxAge(300); // 5 minutes

        return $response;

    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => requirement::DIGITS])]
    #[isGranted(GenericVoter::EDIT, subject: 'livre', message: "Accès refusé", statusCode: 404)]
    public function edit(Request $request, EntityManagerInterface $em, Livre $livre): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->cache->invalidateTags(['livres_list']);

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
