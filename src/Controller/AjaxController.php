<?php


namespace App\Controller;


use App\Entity\AdCategory;
use App\Entity\AdSubCategory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * @Route("/ajax")
 */
class AjaxController extends AbstractController
{

    /**
     * @Route("/ad-subcategories", name="ajax_ad_subcategories", methods={"GET"}, options={"expose"=true})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function ajaxListSubCategories(Request $request, TranslatorInterface $translator){

        $em = $this->getDoctrine()->getManager();

        $req = $request->query->get('category');
        $category = $em->getRepository(AdCategory::class)->findOneBy(array('short_name'=>$req));
        $sub_cat = $em->getRepository(AdSubCategory::class)->findBy(array('adCategory'=>$category->getId()));

        $sub_categories = array();

        foreach ($sub_cat as $key => $sc){
            $sub_categories[$key]['shortname_translated'] = $translator->trans($sc->getShortName(),[],'advert',null);
            $sub_categories[$key]['shortname'] = $sc->getShortName();
        }
        
        return new JsonResponse($sub_categories);

    }



}