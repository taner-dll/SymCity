<?php


namespace App\Controller;


use App\Entity\AdCategory;
use App\Entity\AdSubCategory;
use App\Entity\BusinessCategory;
use App\Entity\FeedBack;
use App\Entity\Place;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * @Route("/ajax")
 */
class AjaxController extends AbstractController
{

    /**
     * @Route("/ajax-send-feedback", name="ajax_send_feedback", methods={"POST"}, options={"expose"=true})
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function ajaxSendFeedBack(Request $request): JsonResponse
    {

        $em = $this->getDoctrine()->getManager();

        $subject = $request->request->get('subject');
        $message = $request->request->get('message');

        $feedback = new FeedBack();
        $feedback->setTopic($subject);
        $feedback->setMessage($message);
        $feedback->setStatus(false);
        $feedback->setCreatedAt(new \DateTime('now'));
        $feedback->setUser($this->getUser());


        $em->persist($feedback);
        $em->flush();

        return new JsonResponse('feedback has been successfully sent', Response::HTTP_OK);


    }

    /**
     * @Route("/get-place-neighborhoods", name="ajax_get_place_neighborhoods", methods={"GET"}, options={"expose"=true})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function ajaxGetPlaceNeighborhoods(Request $request, TranslatorInterface $translator): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $req_id = $request->query->get('place_id');
        if ($req_id){
            $sub_places = $em->getRepository(Place::class)
                ->findBy(array('parent' => $em->find(Place::class, $req_id)), array('name' => 'ASC'));
        }
        else{
            $place = $em->getRepository(Place::class)->findOneBy(array('slug'=> $request->query->get('place')));

            //echo $place->getId();exit;
            $sub_places = $em->getRepository(Place::class)
                ->findBy(array(
                    'parent' => $place->getId()) ,
                    array('name' => 'ASC'));
        }

        $sub_places_arr = [];

        foreach ($sub_places as $key => $value) {

            $sub_places_arr[$key]['id'] = $value->getId();
            $sub_places_arr[$key]['name'] = $value->getName();
            $sub_places_arr[$key]['short_name'] = $value->getSlug();
        }

        return new JsonResponse($sub_places_arr);
    }

    /**
     * @Route("/get-sub-categories", name="ajax_get_sub_categories", methods={"GET"}, options={"expose"=true})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function ajaxGetSubCategories(Request $request, TranslatorInterface $translator): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $req = $request->query->get('id');


        $categories = $em->getRepository(AdCategory::class)->findOneBy(array('short_name'=>$req));

        $sub_categories = $em->getRepository(AdSubCategory::class)
            ->findBy(
                array(
                    'adCategory' => $em->find(AdCategory::class, $categories->getId()),
                    'active'=>true
                ),
                array('sort' => 'ASC'));

        //dump($sub_categories);exit;

        $sub_cats_arr = [];

        foreach ($sub_categories as $key => $value) {

            $sub_cats_arr[$key]['id'] = $value->getId();
            $sub_cats_arr[$key]['name'] = $translator->trans($value->getShortName(), [], 'advert');
            $sub_cats_arr[$key]['short_name']=$value->getShortName();
        }

        return new JsonResponse($sub_cats_arr);
    }


    /**
     * @Route("/ad-subcategories", name="ajax_ad_subcategories", methods={"GET"}, options={"expose"=true})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function ajaxListSubCategories(Request $request, TranslatorInterface $translator): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $req = $request->query->get('category');
        $sub_cat = $em->getRepository(AdSubCategory::class)->findBy(array('adCategory' => $req), array('sort' => 'ASC'));

        if (!$sub_cat) {
            $sub_cat = $em->getRepository(AdSubCategory::class)->findBy(array('id' => $req), array('sort' => 'ASC'));
        }

        $sub_categories = array();
        foreach ($sub_cat as $key => $sc) {
            $sub_categories[$key]['shortname_translated'] = $translator->trans($sc->getShortName()
                , [], 'advert', null);
            $sub_categories[$key]['shortname'] = $sc->getShortName();
            $sub_categories[$key]['id'] = $sc->getId();
        }
        return new JsonResponse($sub_categories);
    }

    /**
     * @Route("/ajax-ad-subcategories-sort", name="ajax_ad_subcategories_sort",
     *     methods={"POST"}, options={"expose"=true})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function ajaxAdSubCategoriesSort(Request $request, TranslatorInterface $translator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $sort_list = $request->request->get('sortlist');
        $category = $request->request->get('cat');
        $cat = $em->getRepository(AdCategory::class)->findOneBy(array('short_name' => $category));

        foreach ($sort_list as $sl) {
            $short_name = $sl[0];
            $new_sort = $sl[1];
            $ad_sub_category = $em->getRepository(AdSubCategory::class)
                ->findOneBy(
                    array(
                        'adCategory' => $cat,
                        'short_name' => $short_name
                    )
                );
            $ad_sub_category->setSort($new_sort);
        }
        $em->flush();
        return new JsonResponse('ok', Response::HTTP_OK);
    }

    /**
     * @Route("/ajax-ad-categories-sort", name="ajax_ad_categories_sort",
     *     methods={"POST"}, options={"expose"=true})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function ajaxAdCategoriesSort(Request $request, TranslatorInterface $translator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $sort_list = $request->request->get('sortlist');
        //echo json_encode($sort_list, JSON_PRETTY_PRINT);
        foreach ($sort_list as $sl) {
            $short_name = $sl[0];
            $new_sort = $sl[1];
            $cat = $em->getRepository(AdCategory::class)->findOneBy(array('short_name' => $short_name));
            $cat->setSort($new_sort);
        }
        $em->flush();
        return new JsonResponse('ok', Response::HTTP_OK);
    }

    /**
     * @Route("/ajax-business-categories-sort", name="ajax_business_categories_sort",
     *     methods={"POST"}, options={"expose"=true})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function ajaxBusinessCategoriesSort(Request $request, TranslatorInterface $translator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $sort_list = $request->request->get('sortlist');
        //echo json_encode($sort_list, JSON_PRETTY_PRINT);
        foreach ($sort_list as $sl) {
            $short_name = $sl[0];
            $new_sort = $sl[1];
            $cat = $em->getRepository(BusinessCategory::class)->findOneBy(array('short_name' => $short_name));
            $cat->setSort($new_sort);
        }
        $em->flush();
        return new JsonResponse('ok', Response::HTTP_OK);
    }

}