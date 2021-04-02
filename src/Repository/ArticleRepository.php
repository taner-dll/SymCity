<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

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

    public function articleFilter(Request $request)
    {

        $em = $this->getEntityManager();

        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->innerJoin('a.user', 'u')
            ->where('a.confirm = :confirm')
            ->setParameter('confirm', 1);

        if ($request->query->get('title')):
            $qb->andWhere($qb->expr()->like('a.title', ':title'))
                ->setParameter('title', '%' . $request->query->get('title') . '%');
        endif;

        if ($request->query->get('author')):
            $qb->andWhere('a.user = :user')
                ->setParameter('user', $request->query->get('author'));
        endif;

        $qb->andWhere('u.author is not null');

        return $qb->getQuery()->execute();
    }


    public function articlesHaveUser()
    {

        $em = $this->getEntityManager();

        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->innerJoin('a.user', 'u')

            ->where('a.confirm = :confirm')
            ->andWhere('u.author is not null')
            ->setParameter('confirm', 1)
            ->orderBy('a.id','desc');



        return $qb->getQuery()->execute();
    }

/*    public function articleHasUser($id)
    {

        $em = $this->getEntityManager();

        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->innerJoin('a.user', 'u')

            ->where('a.confirm = :confirm')
            ->andWhere('u.author is not null')
            ->andWhere('a.id = :id')
            ->setParameter('id',$id)
            ->setParameter('confirm', 1)
            ->orderBy('a.id','desc');



        return $qb->getQuery()->execute();
    }*/


}
