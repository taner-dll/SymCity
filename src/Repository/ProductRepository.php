<?php
/**
 * Created by PhpStorm.
 * User: tnrdll
 * Date: 2.01.2019
 * Time: 13:23
 */

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{



    public function collectionGroupProducts($cg_id,$colg){

        if($colg){
            return $this->createQueryBuilder('p')
                ->innerJoin('p.collection','pc')
                ->innerJoin('pc.collectionGroup', 'cg')
                ->where('cg.id = :cg_id')
                ->andWhere('pc.id = :colg_id')
                ->setParameter('cg_id',$cg_id)
                ->setParameter('colg_id',$colg)
                ->groupBy('p.id')
                ->getQuery()
                ->getResult();
        }
        else{


            return $this->createQueryBuilder('p')
                ->innerJoin('p.collection','pc')
                ->innerJoin('pc.collectionGroup', 'cg')
                ->where('cg.id = :cg_id')
                ->setParameter('cg_id',$cg_id)
                ->groupBy('p.id')
                ->getQuery()
                ->getResult();

        }


    }

    public function customProducts(){

        return $this->createQueryBuilder('p')
            ->where('p.property = :prop')
            ->setParameter('prop','custom')
            ->getQuery()
            ->getResult();
    }

}