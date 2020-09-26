<?php

namespace App\Controller;

use App\Entity\AdCategory;
use App\Entity\AdSubCategory;
use App\Entity\Advert;
use App\Entity\Announce;
use App\Entity\Business;
use App\Entity\BusinessCategory;
use App\Entity\Event;
use App\Entity\Place;
use App\Traits\Util;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\PlacesToVisit;

class WebSiteController extends AbstractController
{

    use Util;


    /**
     * Ana Sayfa
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();

        //yayındaki işletmeler
        $business = $em->getRepository(Business::class)
            ->findBy(array('confirm' => 1));

        $business_category = $em->getRepository(BusinessCategory::class)->findAll();
        $advert_category = $em->getRepository(AdCategory::class)->findAll();
        $places = $em->getRepository(Place::class)->findAll();


        return $this->render('web_site/pages/main.html.twig',
            array(
                'business_count' => $business,
                'business_category' => $business_category,
                'advert_category' => $advert_category,
                'places' => $places
            ));
    }


    /**
     * Arama Sonuçları
     * @Route({
     *     "en": "/search-router",
     *     "tr": "/arama-yonlendir"
     * }, name="search_router",  methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function searchRouter(Request $request)
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


    /**
     * Ücretsiz Ekle
     * @Route({
     *     "en": "/add-to-lists-for-free",
     *     "tr": "/ucretsiz-ekle"
     * }, name="add_to_lists",  methods={"GET"})
     * @return Response
     */
    public function addToLists()
    {
        return $this->render('web_site/pages/add_to_lists.html.twig');
    }




    /**
     * Gezi Rehberi
     * @Route("/travel-guide-embedded-menu", name="travel_guide_embedded_menu")
     * header embedded controller
     */
    public function travelGuideEmbeddedMenu(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $ptv = $em->getRepository(PlacesToVisit::class)->findBy(array(), array('place' => 'ASC'));
        return $this->render('web_site/embedded_controller/travel_guide_embedded_menu.html.twig', ['ptv' => $ptv]);
    }

    /**
     * Gezilecek Yer - Detay Sayfası
     * @Route({
     *     "en": "{city}/places-to-visit/{district}/{slug}",
     *     "tr": "{city}/gezilecek-yerler/{district}/{slug}"
     * }, name="ptv_page",  methods={"GET"})
     * @param $slug
     * @return Response
     */
    public function ptv_page($slug): Response
    {
        $em = $this->getDoctrine()->getManager();
        $ptv = $em->getRepository(PlacesToVisit::class)->findOneBy(array('slug' => $slug));
        return $this->render('web_site/pages/ptv.html.twig', [
            'ptv' => $ptv,
        ]);
    }








    #ETKİNLİKLER BAŞLANGIÇ

    /**
     * @Route({
     *     "en": "/events",
     *     "tr": "/etkinlikler"
     * }, name="events",  methods={"GET"})
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function events(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();


        $events = $em->getRepository(Event::class)->eventFilter($request);

        $events = $paginator->paginate(
            $events,
            $request->query->getInt('page', 1), 5
        );

        return $this->render('web_site/pages/events.html.twig', [
            'events' => $events,
            /*'categories' => $em->getRepository(Event::class)->advertCategoryList(),*/
            'places' => $em->getRepository(Place::class)->findAll()
        ]);
    }

    /**
     * @Route({
     *     "en": "/event-detail/{id}/{slug}",
     *     "tr": "/etkinlik-detay/{id}/{slug}"
     * }, name="event_detail",  methods={"GET"})
     * @param $id
     * @return Response
     */
    public function event_detail($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $events_detail = $em->getRepository(Event::class)->findOneBy(
            array('confirm' => 1, 'id' => $id));

        return $this->render('web_site/pages/event_detail.html.twig', [
            'event_detail' => $events_detail,
        ]);

    }
    #ETKİNLİKLER SON


    #DUYURULAR BAŞLANGIÇ

    /**
     * @Route({
     *     "en": "/announces",
     *     "tr": "/duyurular"
     * }, name="announces",  methods={"GET"})
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function announces(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();

        $announces = $em->getRepository(Announce::class)->announceFilter($request);

        $announces = $paginator->paginate(
            $announces,
            $request->query->getInt('page', 1), 5
        );

        return $this->render('web_site/pages/announces.html.twig', [
            'announces' => $announces,
            /*'categories' => $em->getRepository(Event::class)->advertCategoryList(),*/
            'places' => $em->getRepository(Place::class)->findAll()
        ]);
    }

    /**
     * @Route({
     *     "en": "/announce-detail/{id}/{slug}",
     *     "tr": "/duyuru-detay/{id}/{slug}"
     * }, name="announce_detail",  methods={"GET"})
     * @param $id
     * @return Response
     */
    public function announce_detail($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $announce_detail = $em->getRepository(Announce::class)->findOneBy(
            array('confirm' => 1, 'id' => $id));

        return $this->render('web_site/pages/announce_detail.html.twig', [
            'announce_detail' => $announce_detail,
        ]);

    }

    #DUYURULAR SON


    #İLANLAR BAŞLANGIÇ
    /**
     * @Route({
     *     "en": "/adverts",
     *     "tr": "/ilanlar"
     * }, name="adverts",  methods={"GET"})
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function adverts(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();

        $adverts = $em->getRepository(Advert::class)->advertFilter($request);

        $adverts = $paginator->paginate(
            $adverts,
            $request->query->getInt('page', 1), 5
        );


        return $this->render('web_site/pages/adverts.html.twig', [
            'adverts' => $adverts,
            'categories' => $em->getRepository(Advert::class)->advertCategoryList(),
            'sub_categories' => $em->getRepository(Advert::class)->advertSubCategoryList(),
            'places' => $em->getRepository(Place::class)->findAll()
        ]);
    }

    /**
     * @Route({
     *     "en": "/advert-detail/{id}/{cat}/{sub}/{slug}",
     *     "tr": "/ilan-detay/{id}/{cat}/{sub}/{slug}"
     * }, name="advert_detail",  methods={"GET"})
     * @param $id
     * @return Response
     */
    public function advert_detail($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $advertes_detail = $em->getRepository(Advert::class)->findOneBy(
            array('confirm' => 1, 'id' => $id));

        return $this->render('web_site/pages/advert_detail.html.twig', [
            'advert_detail' => $advertes_detail,
        ]);

    }
    #İLANLAR SON


    #İŞLETME BAŞLANGIÇ
    /**
     * @Route({
     *     "en": "/business-guide",
     *     "tr": "/isletme-rehberi"
     * }, name="business_guide",  methods={"GET"})
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function business_guide(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();

        $businesses = $em->getRepository(Business::class)->businessGuideFilter($request);

        $businesses = $paginator->paginate(
            $businesses,
            $request->query->getInt('page', 1), 5
        );

        return $this->render('web_site/pages/business_guide.html.twig', [
            'businesses' => $businesses,
            'categories' => $em->getRepository(Business::class)->businessCategoryList(),
            'places' => $em->getRepository(Place::class)->getDistricts(),
            'sub_places' => $em->getRepository(Place::class)->getNeighborhoods()
        ]);
    }

    /**
     * @Route({
     *     "en": "/business-detail/{id}/{slug}",
     *     "tr": "/isletme-detay/{id}/{slug}"
     * }, name="business_detail",  methods={"GET"})
     * @param $id
     * @return Response
     */
    public function business_detail($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $businesses_detail = $em->getRepository(Business::class)->findOneBy(
            array('confirm' => 1, 'id' => $id));

        return $this->render('web_site/pages/business_detail.html.twig', [
            'business_detail' => $businesses_detail,
        ]);

    }
    #İŞLETME SON


    /**
     * İlanlar Dinamik Menüsü
     * @Route("/advert_menu", name="advert_menu")
     * header embed controller
     * ilanlar menüsü
     */
    public function advertMenu(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $ad_categories = $em->getRepository(AdCategory::class)->findBy(array('active' => 1), array('sort' => 'ASC'));
        return $this->render('web_site/embedded_controller/advert_menu.html.twig',
            ['ad_categories' => $ad_categories]);
    }


}
