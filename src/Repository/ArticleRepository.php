<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function myFindAllWithPaging($currentPage, $nbPerPage)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.published = true')
            ->leftJoin('a.comments','c')
            ->addSelect('c')
            ->orderBy('c.created_at', 'desc')
            ->orderBy('a.created_at','desc')
            ->setFirstResult(($currentPage - 1) * $nbPerPage)
            ->setMaxResults($nbPerPage)
        ;

        return new Paginator($query);
    }

    public function findWithCategories($id)
    {
        return $this->createQueryBuilder('a')
            ->where('a.id = :id')
            ->setParameter('id', $id)
            ->andWhere('a.published >= :published')
            ->setParameter('published' , true)
            ->leftJoin('a.categories', 'ca')
            ->addSelect('ca')
            ->getQuery()->getOneOrNullResult();
    }

    public function findAllArticleWithCategory($id, $currentPage, $nbPerPage)
    {
        $query = $this->createQueryBuilder('a')
            ->Where('cat.id = :id')
            ->setParameter('id', $id)
            ->andWhere('a.published = true')
            ->leftJoin('a.categories', 'cat')
            ->addSelect('cat')
            ->orderBy('a.created_at', 'desc')
            ->setFirstResult(($currentPage - 1) * $nbPerPage)
            ->setMaxResults($nbPerPage);

        return new Paginator($query);
    }

    // /**
    //  * @return ArticleFixtures[] Returns an array of ArticleFixtures objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ArticleFixtures
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
