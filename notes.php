<?php 

/*

1- Entité Livre 
Possède une seule Category => ManyToOne
Exple : A, B 

2- Entité Category 
 Possède plusieurs livres => OneToMany(1 Category -> N livres)
 Exple : Science-fiction

php bin/console make:entity Livre
nom du champ: category
relation => ManyToOne
Cible : Category

Dans Livre
#[ORM\ManyToOne(inversedBy: 'livres')]
private ?Category $category = null;

Dans Category
#[ORM\OneToMany(mappedBy 'category', targetEntity: livre:class)]
private  Collection  $livres; // Collection de livres

// Partie Ap
1- Les services => OK
2- Security => OK
3- Voter => OK
4- Internationalisation => OK
5- Performance => OK

*/