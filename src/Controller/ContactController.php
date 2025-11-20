<?php 

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]  
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $data = $form->getData();

            // Créez l'email
            $email = (new Email())
                ->from('dawan@tonentreprise.com') 
                ->to('devtechkandia@gmail.com') // Destinataire
                ->subject('Nouveau message de contact') // Sujet de l'email
                ->html('<p><strong>Nom: </strong>' . $data['nom'] . '</p>
                        <p><strong>Prénom: </strong>' . $data['prenom'] . '</p> 
                        <p><strong>Email: </strong>' . $data['email'] . '</p> 
                        <p><strong>Numéro: </strong>' . $data['numero'] . '</p>
                        <p><strong>Message: </strong><br>' . nl2br($data['content']) . '</p>');

                // Envoi de l'email
                try {
                    $mailer->send($email);
                    $this->addFlash('success', 'Votre message a été envoyé avec succès.');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'envoi de votre. Veuillez réessayer');
                }

                return $this->redirectToRoute('app_contact');


        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
