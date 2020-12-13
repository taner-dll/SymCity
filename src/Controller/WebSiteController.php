<?php

namespace App\Controller;

use App\Entity\AdCategory;
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
use Symfony\Contracts\Translation\TranslatorInterface;

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

        $business_category = $em->getRepository(BusinessCategory::class)->businessCategorySort();
        $advert_category = $em->getRepository(AdCategory::class)->adCategorySort();
        $places = $em->getRepository(Place::class)->findAll();



        return $this->render('web_site/pages/main.html.twig',
            array(
                'business_count' => $business,
                'business_category' => $business_category,
                'advert_category' => $advert_category,
                'places' => $places,
                'business'=> $business
            ));

    }


    /**
     * Ücretsiz Ekle
     * @Route({
     *     "en": "/add-to-lists-for-free",
     *     "tr": "/ucretsiz-ekle"
     * }, name="add_to_lists",  methods={"GET"})
     * @return Response
     */
    public function addToLists(): Response
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
     * @Route({"en": "adverts","tr": "ilanlar"},
     *     name="adverts",  methods={"GET"})
     * )
     * @Route({"en": "adverts/category/{cat}","tr": "ilanlar/kategori/{cat}"},
     *     name="adverts_cat",  methods={"GET"})
     * )
     * @Route({"en": "adverts/category/{cat}/{sub_cat}","tr": "ilanlar/kategori/{cat}/{sub_cat}"},
     *     name="adverts_sub_cat",  methods={"GET"})
     * )
     * @Route({"en": "adverts/place/{place}/category/{cat}/{sub_cat}","tr": "ilanlar/bolge/{place}/kategori/{cat}/{sub_cat}"},
     *     name="adverts_sub_cat_place",  methods={"GET"})
     * )
     * @Route({"en": "adverts/place/{place}","tr": "ilanlar/bolge/{place}"},
     *     name="adverts_place",  methods={"GET"})
     * )
     * @Route({"en": "adverts/place/{place}/{sub_place}","tr": "ilanlar/bolge/{place}/{sub_place}"},
     *     name="adverts_sub_place",  methods={"GET"})
     * )
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param null $cat
     * @param null $sub_cat
     * @param null $place
     * @param null $sub_place
     * @param null $title
     * @return Response
     *
     * Parametreler boş gelebileceğinden, null olarak tanımladık.
     */
    public function adverts(Request $request, PaginatorInterface $paginator,
                            $cat = null, $sub_cat = null, $place = null, $sub_place = null,
                            $title = null

    ): Response
    {

        $route = $request->get('_route');

        //route adverts ise search form ile GET olarak gelmektedir.
        if ($route === 'adverts'):
            $title = $request->query->get('title');
            $cat = $request->query->get('cat');
            $sub_cat = $request->query->get('sub_cat');
            $place = $request->query->get('place');
            $sub_place = $request->query->get('sub_place');
        endif;

        $url_parameters = [
            'cat' => $cat, 'sub_cat' => $sub_cat, 'place' => $place,
            'sub_place' => $sub_place, 'title' => $title
        ];

        $em = $this->getDoctrine()->getManager();

        //gelen parametrelere göre ilanları filtrele
        $adverts = $em->getRepository(Advert::class)
            ->advertFilter($url_parameters, $request);

        $adverts = $paginator->paginate(
            $adverts,
            $request->query->getInt('page', 1), 5
        );

        return $this->render('web_site/pages/ad_guide.twig', [
            'adverts' => $adverts,
            'categories' => $em->getRepository(AdCategory::class)->findBy(array('active' => true), array('sort' => 'ASC')),
            'places' => $em->getRepository(Place::class)->getDistricts()
        ]);
    }

    /**
     * @Route({
     *     "en": "/advert-detail/{id}/{cat}/{sub}/{slug}",
     *     "tr": "/ilan-detay/{id}/{cat}/{sub}/{slug}"
     * }, name="advert_detail",  methods={"GET"}, options={"expose"=true})
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
     * @param TranslatorInterface $translator
     * @param Request $request
     * @return Response
     */
    public function advertMenu(TranslatorInterface $translator, Request $request): Response
    {

        //$request->setLocale('en');
        $em = $this->getDoctrine()->getManager();
        $ad_categories = $em->getRepository(AdCategory::class)->findBy(array('active' => 1), array('sort' => 'ASC'));


        $ad_cats = [];

        //en değilse translate'den getir ve slug yap. Emlak İlanları -> emlak-ilanlari
        if ($request->getLocale() !== 'en') {
            foreach ($ad_categories as $key => $value) {
                $name = $translator->trans($value->getShortName(), [], 'advert', $request->getLocale());
                $name = $this->slugify($name);
                $ad_cats[$key]['translated_url_parameter'] = $name;
                $ad_cats[$key]['short_name'] = $value->getShortName();
            }
        } else {
            foreach ($ad_categories as $key => $value) {
                $ad_cats[$key]['translated_url_parameter'] = $value->getShortName();
                $ad_cats[$key]['short_name'] = $value->getShortName();
            }
        }

        //dump($ad_cats);exit;

        //dump($request->getLocale());exit;


        return $this->render('web_site/embedded_controller/advert_menu.html.twig',
            ['ad_categories' => $ad_cats]);
    }






}
