<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Email;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void 
    {
        $builder
            ->add('nom', TextType::class, [  
                'label' => 'Nom',   
                'required' => true, 
                'attr' => [
                    'placeholder' => 'Entrez votre nom',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom est obligatoire',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s]+$/u',
                        'message' => 'Le nom ne doit contenir que des lettres et des espaces.',
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez votre prénom',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le prénom est obligatoire',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s]+$/u', 
                        'message' => 'Le prénom ne doit contenir que des lettres et des espaces.',
                    ]),
                ],
            ])
            ->add('numero', TelType::class, [
                'label' => 'Numéro de téléphone',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez votre numéro de téléphone',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le numéro de téléphone est obligatoire',
                    ]),
                    new Regex([  
                        'pattern' => '/^\+?\d{10,}$/',  
                        'message' => 'Veuillez entrer un numéro valide, composé uniquement de chiffres (au moins 10 chiffres).',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez votre adresse email',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'L\'adresse email est obligatoire',
                    ]),
                    new Email([
                        'message' => 'L\'adresse email n\'est pas valide.',
                    ]),
                ],
            ])
            ->add('sujet', TextType::class, [
                'label' => 'Sujet',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez le sujet de votre message',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le sujet est obligatoire']),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s\w]+$/u', 
                        'message' => 'Le sujet ne doit contenir que des lettres, des chiffres et des espaces.', 
                    ]),
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Votre message',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez votre message',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le message ne peut pas être vide',
                    ]),
                ],
            ])
            ->add('envoyer', SubmitType::class, [
                'label' => 'Envoyer le message',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'csrf_protection' => false,  
        ]);
    }
}


