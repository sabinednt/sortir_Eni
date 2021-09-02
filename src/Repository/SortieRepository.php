<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    /**
     * @var TokenStorageInterface
     */
    private $TokenStorageInterface;

    public function __construct(ManagerRegistry $registry,
                                TokenStorageInterface $TokenStorageInterface)
    {

        parent::__construct($registry, Sortie::class);
        $this->TokenStorageInterface = $TokenStorageInterface;

    }

    public function findSorties(): Paginator
    {
        $queryBuilder = $this
            ->createQueryBuilder('s')
            ->select('s, p')
            ->leftJoin('s.organisateur', 'p')
        ;

        $query = $queryBuilder->getQuery();
        $query->setMaxResults(50);

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


    public function findOneBySomeField($id)
    {
        return $this->createQueryBuilder('sortie')
            ->andWhere('sortie.id = :id')
            ->setParameter('id', $id)
            ->join('sortie.campus','camp')
            ->addSelect('camp')
            ->join('sortie.lieu', 'l')
            ->addSelect('l')
            ->join('l.ville','ville')
            ->addSelect('ville')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Récupère les sorties en fonctions des critères de recherche
     *
     */
    public function findSearch(SearchData $search):Paginator
    {
        $user = $this->TokenStorageInterface->getToken()->getUser();



        $query = $this
            ->createQueryBuilder('s');

        if (!empty($search->campus)) {
            $query = $query
                ->select('c', 's')
                ->join('s.campus', 'c')
                ->andWhere('c.id = :campus')
                ->setParameter('campus', $search->campus );
        }

        //recherche par mot-clé
        if (!empty($search->q)){
            $query = $query
                ->andWhere('s.nom LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        //recherche par date
        if(!empty($search->dateMin)){
            $query = $query
                ->andWhere ('s.dateHeureDebut >= :dateMin')
                ->setParameter('dateMin', $search->dateMin);
        }


        if(!empty($search->dateMax)){
            $query = $query
                ->andWhere ('s.dateHeureDebut <= :dateMax')
                ->setParameter('dateMax', $search->dateMax);
        }

        //recherche par organisation
        if(!empty($search->organisateur)){
            $query = $query
                ->join('s.organisateur','o')
                ->andWhere ('o.id = :organisateur')
                ->setParameter('organisateur', $user->getId());
        }

       //recherche par participation à des sorties
        if(!empty($search->participant)){
            $query = $query
                ->join('s.participants', 'p')
                ->andWhere ('p.id =:participant')
                ->setParameter('participant', $user->getId());
        }

        //recherche par non-participation aux sorties
        if(!empty($search->nonParticipant)){
            $query = $query
                ->andWhere(':part NOT MEMBER OF s.participants ')
                ->setParameter('part', $user->getId());
        }

        //recherche par sorties passées
        if(!empty($search->sortiesPassees)){
            $now = new \DateTime('now');
            $query = $query
                ->andWhere ('s.dateHeureDebut < :now')
                ->setParameter('now', $now);

        }

        $query = $query->getQuery();
        $paginator = new Paginator($query);

        return $paginator;

    }

}
