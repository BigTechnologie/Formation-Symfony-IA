<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Livre>
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Livre::class);
    }

    public function paginatelivres(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->createQueryBuilder('l')->leftJoin('l.category', 'c')->select('l', 'c'),
            $page,
            4,
            options: [
                'distinct' => true,
                'sortFieldAllowList' => ['l.id', 'l.title']
            ]
        );
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

    // TP5 : Récupère tous les livres ainsi que leur Categorie associée
    public function findRelatedBooksAndCategory(): array 
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.category', 'c')
            ->addSelect('c')
            ->getQuery()
            ->getResult();
    }


    // TP6: Récupère tous les livres appartenant à une catégorie spécifique (Trouver tous les livres d'une catégorie donnée) => id en parametre.
    public function findByCategorie(int $categorieId): array
    {
        $qb = $this->createQueryBuilder('l');
        $qb->join('l.category', 'c');
        $qb->andWhere('c.id = :id')
            ->setParameter('id', $categorieId);
        return $qb->getQuery()->getResult();
    }

    // TP7: Compter le nombre total de livres dans chaque catégorie.
    public function countLivresParCategorie(): array 
    {
        $qb = $this->createQueryBuilder('l');
        $qb->select('c.name AS categorie, COUNT(l.id) AS nbLivres');
        $qb->join('l.category', 'c');
        $qb->groupBy('c.id');

        return $qb->getQuery()->getResult();

    }

    // TP8 : Recherche de livres selon plusieurs critères optionnels : le titre (ou une partie du tire). La catégorieId
    public function search(?string $title = null, ?int $catagorieId = null): array
    {
        $qb = $this->createQueryBuilder('l');

        if($title) {
            $qb->andWhere('l.title LIKE :title')
                ->setParameter('title', "%$title%");
        }

        if($catagorieId) {
            $qb->join('l.category', 'c')
                ->andWhere('c.id = :id')
                ->setParameter('id', $catagorieId);
        }

        return $qb->orderBy('l.publicationYear', 'DESC')
            ->getQuery()
            ->getResult();

    } 

    // TP9 : Trouver les livres d’un author (User). Recherche avancée de livres avec plusieurs critères facultatifs : par titre; par catégorieId; par auteur
    public function searchPlus(?string $title = null, ?int $categorieId = null, ?string $author = null): array 
    {
        $qb = $this->createQueryBuilder(alias: 'l');
         if($title) {
            $qb->andWhere('l.title LIKE :title')
                ->setParameter('title', "%$title%");
        }
  
        if($categorieId) {
            $qb->join('l.category', 'c')
                ->andWhere('c.id = :id')
                ->setParameter('id', $categorieId);
        }

        if($author) {
            $qb->andWhere('l.author = :author')
                ->setParameter('author', $author);
        }

         return $qb->orderBy('l.publicationYear', 'DESC')
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
