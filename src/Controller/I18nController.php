<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/i18n', name: 'i18n_')]
final class I18nController extends AbstractController
{
   #[Route('/{_locale}', name: 'index', requirements: ['_locale' => 'en|fr'])]
    public function index(TranslatorInterface $translator): Response
    {
        $message = $translator->trans('hello');
        return $this->render('i18n/index.html.twig', [
            'message' => $message,
        ]);
    }
}
