<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livre>
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    public function structureGenerale()
    {
        $qb = $this->createQueryBuilder('alias')
            ->select('alias, autreAlias.champ') // select('l)
            ->from('App\Entity\MonEntite', 'alias')
            ->join('alias.association', 'autreAlias')
            ->leftJoin('alias.autreRelation', 'leftAlias')
            ->addSelect('leftAlias.champ')
            ->where('alias.champ = :valeur')
            ->andWhere('alias.autreChamp > :valeur2')
            ->orWhere('alias.champ LIKE : motcle')
            ->groupBy('alias.categorie')
            ->having('COUNT(alias.id) > 5')
            ->orderBy('alias.datecreation', 'DESC')
            ->addOrderBy('alias.nom', 'ASC')

            ->setParameter('valeur', 10)
            ->setParameter('valeur2', 2025)
            ->setParameter('motcle', '%Symfony%')

            ->setMaxResults(20)
            ->setFirstResult(0)

            ->getQuery()
            ->getResult();
    }

    //TP1
    public function findByTitle(string $title): array 
    {
        $title = trim($title);

        $qb = $this->createQueryBuilder('l');

        $qb->andWhere('LOWER(l.title) LIKE LOWER(:title)')
            ->setParameter('title', "%$title%")
            ->orderBy('l.publicationYear', 'DESC');

        return $qb->getQuery()->getResult();

    }

    // TP2 Recherche par auteur
    public function findByauthor(string $author): array 
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.author = :auteur')
            ->setParameter('auteur', $author)
            ->orderBy('l.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // TP 3 : Calcul et retourne la somme totale de toutes les valeurs du champ publicationYear de l'entité
    public function findTotalYear(): int 
    {
        return $this->createQueryBuilder('l')
            ->select('SUM(l.publicationYear) as total')
            ->getQuery()
            ->getSingleScalarResult();
    }
    

    // TP 4 : Trouve et retourne les enregistrements dont l'année de publication est inferieur ou égale à une valeur donnée
    public function findWithPublicationYearLowerThan(int $publicationYear): array 
    {
        return $this->createQueryBuilder('l')
            ->where('l.publicationYear <= :publicationYear')
            ->orderBy('l.publicationYear', 'ASC')
            ->setMaxResults(5)
            ->setParameter('publicationYear', $publicationYear)
            ->getQuery()
            ->getResult();
    }









//    /**
//     * @return Livre[] Returns an array of Livre objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Livre
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
