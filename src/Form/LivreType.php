<?php

namespace App\Form;

use App\Entity\Livre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;

class LivreType extends AbstractType
{
    public function __construct(private FormListenerFactory $listenerFactory){}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'empty_data' => ''
            ])
            ->add('slug', TextType::class, [
                'required' => false
            ])
            ->add('author', TextType::class, [
                'required' => false
            ])
            ->add('publicationYear', IntegerType::class, [
                'empty_data' => 0
            ])
            /*
            ->add('createdAt', null, [
                'widget' => 'single_text',
                'empty_data' => (new \DateTime())->format('Y-m-d')
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text',
                'empty_data' => (new \DateTime())->format('Y-m-d')
            ])
            */
            ->add('genre', TextType::class, [
                'empty_data' => ''
            ])
            ->add('summary', TextType::class, [
                'empty_data' => ''
            ])
            ->add('publisher', TextType::class, [
                'empty_data' => ''
            ])
            ->add('language', TextType::class, [
                'empty_data' => ''
            ])
            ->add('edition', TextType::class, [
                'empty_data' => ''
            ])
            ->add('coverImage', TextType::class, [
                'required' => false,
                'empty_data' => ''
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer'
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->listenerFactory->autoslug('title'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->listenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}
