<?php

namespace App\Controller;

use App\ValueObjet\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TwigController extends AbstractController
{
    #[Route('/twig', name: 'twig_index')]
    public function index(): Response
    {
        $date = new \DateTime();

        $html = "<b>Hello World</b>";

        return $this->render('twig/index.html.twig', [
            'name' => 'Stéphane',
            'date' => $date,
            'html' => $html
        ]);
    }

     #[Route('/twig/structure', name: 'twig_structure')]
    public function structure(): Response
    {
       $contact = [
        'firstname' => "John",
        'lastname' => 'Doe',
        'email' => "admin@dawan.fr"
       ];

       $objectContact = new Contact('Dawan', null, true);
       $templateName = "structure";

       return $this->render('twig/' . $templateName . '.html.twig', [
        'contact' => $contact,
        'object_contact' => $objectContact
       ]);


    }



}
