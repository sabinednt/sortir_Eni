<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findSorties(): Paginator
    {
        // SELECT * FROM sortie LEFT JOIN participant ON sortie.organisateur_id = participant.id
        // DQL
        // $entityManager = $this->getEntityManager();
        // $dql = "SELECT s
        //         FROM App\Entity\Serie s
        //         WHERE s.popularity > 70
        //         AND s.vote > 2
        //         ORDER BY s.popularity DESC
        // ";
        // $query = $entityManager->createQuery($dql);
        // $query->setMaxResults(50);
        // $results = $query->getResult();

        //  QUERY BUILDER
        // $queryBuilder = $this->createQueryBuilder('s');

        $queryBuilder = $this
            ->createQueryBuilder('s')
            ->select('s, p')
            ->leftJoin('s.organisateur', 'p')
        ;

        // $queryBuilder->leftJoin('s.organisateur', 'participant')
        //     ->addSelect('participant');

        // $queryBuilder->leftJoin('s.participants', 'participants')
        //     ->addSelect('participants');

        // $queryBuilder->andWhere('s.organisateur_id = participant.id');
        // $queryBuilder->andWhere('s.vote > 1');
        // $queryBuilder->addOrderBy('s.dateHeureDebut', 'DESC');

        $query = $queryBuilder->getQuery();
        $query->setMaxResults(50);

        dump($query);

        $paginator = new Paginator($query);

        return $paginator;
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}