<?php

namespace App\Controller;

use App\Entity\AdCategory;
use App\Entity\BusinessCategory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/search")
 */
class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function index()
    {
        /*return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
        ]);*/
    }


    /**
     * Arama Sonuçları
     * @Route({
     *     "en": "/router",
     *     "tr": "/yonlendir"
     * }, name="search_router",  methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function searchRouter(Request $request): ?Response
    {

        $em = $this->getDoctrine()->getManager();

        //genel GET parametreler
        $category = $request->request->get('category');
        $word = $request->request->get('word');
        $location = $request->request->get('location');

        if ($location == 'all' || $location == ''):
            $location = null;
        endif;

        if (!$word):
            $word = null;
        endif;


        //spesifik GET parametereleri
        $business_category = $request->request->get('business_category');
        $advert_category = $request->request->get('advert_category');
        $advert_subcategory = $request->request->get('advert_subcategory');


        if ($category === 'business') {

            if ($business_category) {
                if ($business_category == 'all'):
                    $cat = null;
                else:
                    $cat = $em->find(BusinessCategory::class, $business_category)->getShortName();
                endif;
            } else {
                $cat = null;
            }

            return $this->redirect($this->generateUrl('business_guide', array(
                'name' => $word,
                'cat' => $cat,
                'place' => $location
            )));
        }
        # category == business end


        if ($category === 'advert') {

            if ($advert_category) {

                if ($advert_category == 'all'):
                    $cat = null;
                else:
                    $cat = $em->find(AdCategory::class, $advert_category)->getShortName();
                endif;

                if ($advert_subcategory == 'all'):
                    $sub = null;
                else:
                    $sub = $advert_subcategory; //car, motorcycle...
                endif;



            } else {
                $cat = null;
                $sub = null;
            }

            return $this->redirect($this->generateUrl('adverts', array(
                'name' => $word,
                'cat' => $cat,
                'place' => $location,
                'sub'=> $sub
            )));
        }
        # category == business end


        if ($category === 'event') {
            return $this->redirect($this->generateUrl('events', array(
                'name' => $word,
                'place' => $location
            )));
        }
        # category == event end


        if ($category === 'announce') {
            return $this->redirect($this->generateUrl('announces', array(
                'name' => $word,
                'place' => $location
            )));
        }
        # category == announce end


    }


}
