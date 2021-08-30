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

  /*  public function findSortieForAnnul($id){
  $qb = $this->createQueryBuilder('sortie');

    //jointure avec l'entité Campus
    $qb ->join('sortie.campus', 'camp');
    $qb->addSelect('camp');
    $qb->where('sortie.id=:id');
    $qb->setParameter('id',$id);

   // $qb->leftJoin('sortie.lieu', '')
   //     ->addSelect('lieu');
    $qb ->setMaxResults(10);
    $query=$qb->getQuery();


 //   $paginator=new Paginator($query);
    return $query->getResult();

    }*/



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

}
