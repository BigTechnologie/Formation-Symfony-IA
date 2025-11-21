<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Livre;

class LivreFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private readonly SluggerInterface $slugger){}
    public function load(ObjectManager $manager): void
    {
       $faker = Factory::create('fr_FR');

       // Création des catégories
       $categories = ['Science-fiction', 'Biographie', 'Histoire', 'Fantasy', 'Thriller', 'Roman'];
       foreach($categories as $c) {
            $category = (new Category())
                ->setName($c)
                ->setSlug($this->slugger->slug($c))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setcreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()));
            $manager->persist($category);
            $this->addReference($c, $category);
       }

       // Création des Livres
       for($i = 1; $i <= 20; $i++) {
            $title = $faker->sentence(3);
            $author = $faker->name;
            $livre = (new Livre())
                ->setTitle($title)
                ->setSlug($this->slugger->slug($title))
                ->setAuthor($author)
                ->setPublicationYear($faker->year)
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setcreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setSummary($faker->paragraphs(3, true))
                ->setPublisher($faker->company)
                ->setLanguage($faker->languageCode)
                ->setGenre($faker->randomElement(['Science-fiction', 'Non-Fiction', 'Mystery', 'Adventure']))
                ->setEdition($faker->randomElement(['1st', '2nd', 'Revised']))
                ->setCoverImage('https://picsum.photos/200/200?random=' . $faker->numberBetween(1, 1000))
                ->setCategory($this->getReference($faker->randomElement($categories), Category::class));

                //Associer les 5 premiers livre à admin
                if($i <= 5) {
                    $livre->setUser($this->getReference(UserFixtures::ADMIN, User::class));
                }else {
                    $livre->setUser($this->getReference('USER' . $faker->numberBetween(1, 10), User::class));
                }

                $manager->persist($livre);
       }



        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }


}
