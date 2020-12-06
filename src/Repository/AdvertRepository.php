<?php

namespace App\Repository;

use App\Entity\AdSubCategory;
use App\Entity\Advert;
use App\Traits\File;
use App\Traits\Util;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Proxies\__CG__\App\Entity\AdCategory;
use Symfony\Bridge\Doctrine\RegistryInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;




/**
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRepository extends ServiceEntityRepository
{

    use Util;

    private $translator;

    public function __construct(RegistryInterface $registry, TranslatorInterface $translator)
    {
        parent::__construct($registry, Advert::class);
        $this->translator = $translator;
    }

    /**
     * Adverts page - right colum, category list.
     * @return Advert[]
     */
    public function advertCategoryList(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.id as id, count(c.id) as total, c.short_name as shortname, sc.short_name as sc_shortname')
            ->innerJoin('a.category', 'c')
            ->innerJoin('a.sub_category','sc')
            ->where('a.confirm = :confirm')
            ->setParameter('confirm',1)
            ->groupBy('shortname')
            ->orderBy('c.sort','asc')
            ->getQuery();
        return $qb->execute();
    }

    /**
     * Adverts page - right colum, sub category list.
     * @return Advert[]
     */
    public function advertSubCategoryList(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.id as id, count(sc.id) as total, sc.short_name as shortname')
            ->innerJoin('a.sub_category', 'sc')
            ->where('a.confirm = :confirm')
            ->setParameter('confirm',1)
            ->groupBy('shortname')
            ->orderBy('sc.sort','asc')
            ->getQuery();
        return $qb->execute();
    }

    public function advertFilter($params, Request $request){

        $em = $this->getEntityManager();

        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->leftJoin('a.category', 'c')
            ->leftJoin('a.place', 'place')
            ->leftJoin('a.sub_place', 'sub_place')
            ->leftJoin('a.sub_category', 'sc')
            ->where('a.confirm = :confirm')
            ->setParameter('confirm',1)
            ->orderBy('a.id','desc');

        if (isset($params['title'])):
            $qb->andWhere($qb->expr()->like('a.title',':bname'))
                ->setParameter('bname','%'.$params['title'].'%');
        endif;

        if (isset($params['cat'])):
            $cat_short_name = $params['cat'];

            //sef linkler için eşleştirme yapıyoruz.
            if ($request->getLocale()!=='en'){
                $categories = $em->getRepository(AdCategory::class)->findAll();
                $ad_cats = [];
                foreach ($categories as $key => $value){
                    $ad_cats[$key]['name'] = $this->slugify(
                        $this->translator->trans($value->getShortName(), [], 'advert',$request->getLocale()));
                    if ($params['cat'] === $ad_cats[$key]['name']){
                        $cat_short_name = $value->getShortName();
                        break;
                    }
                }
            }
            $qb->andWhere('c.short_name = :sname')
                ->setParameter('sname',$cat_short_name);
        endif;

        if (isset($params['sub_cat'])):
            $sub_cat_short_name = $params['sub_cat'];
            //sef linkler için locale eşleştirme yapıyoruz.
            if ($request->getLocale()!=='en'){
                $categories = $em->getRepository(AdSubCategory::class)->findAll();
                $ad_sub_cats = [];
                foreach ($categories as $key => $value){
                    $ad_sub_cats[$key]['name'] = $this->slugify(
                        $this->translator->trans($value->getShortName(), [], 'advert',$request->getLocale()));
                    if ($params['sub_cat'] === $ad_sub_cats[$key]['name']){
                        $sub_cat_short_name = $value->getShortName();
                        break;
                    }
                }
            }
            $qb->andWhere('sc.short_name = :scname')
                ->setParameter('scname',$sub_cat_short_name);
        endif;

        //bölge
        if (isset($params['place'])):
            $qb->andWhere('place.slug = :place')
                ->setParameter('place',$params['place']);
        endif;

        if (isset($params['sub_place'])):
            $qb->andWhere('sub_place.slug = :sub_place')
                ->setParameter('sub_place',$params['sub_place']);
        endif;


        return $qb->getQuery()->execute();
    }
}
