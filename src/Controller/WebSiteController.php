<?php

namespace App\Controller;

use App\Entity\AdCategory;
use App\Entity\Advert;
use App\Entity\Announce;
use App\Entity\Article;
use App\Entity\Business;
use App\Entity\BusinessCategory;
use App\Entity\Event;
use App\Entity\Place;
use App\Entity\PTVCategory;
use App\Traits\Util;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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


        //son eklenen işletmeler
        //todo öne çıkan işletmeler (vitrin)
        $business = $em->getRepository(Business::class)
            ->findBy(array('confirm' => 1),array('id'=>'desc'),6);

        $business_category = $em->getRepository(BusinessCategory::class)->businessCategorySort();
        $advert_category = $em->getRepository(AdCategory::class)->adCategorySort();
        $places = $em->getRepository(Place::class)->getDistricts();

        $articles = $em->getRepository(Article::class)->articlesHaveUser();
        //dump(count($articles));exit;

        return $this->render('web_site/pages/main.html.twig',
            array(
                'business_count' => $business,
                'business_category' => $business_category,
                'advert_category' => $advert_category,
                'places' => $places,
                'business' => $business,
                'articles' => $articles
            ));
    }

    /**
     * @Route({
     *     "en": "/articles",
     *     "tr": "/kose-yazilari"
     * }, name="articles",  methods={"GET"})
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function articles(Request $request, PaginatorInterface $paginator)
    {

        $em = $this->getDoctrine()->getManager();
        //$articles = $em->getRepository(Article::class)->findBy(['confirm'=>1],['id'=>'DESC']);

        $articles = $em->getRepository(Article::class)->articleFilter($request);

        $articles = $paginator->paginate(
            $articles,
            $request->query->getInt('page', 1), 5
        );

        return $this->render('web_site/pages/articles.html.twig', [
            'articles' => $articles
        ]);

    }

    /**
     * @Route({
     *     "en": "/articles/{id}",
     *     "tr": "/kose-yazilari/{id}"
     * }, name="article_detail",  methods={"GET"})
     * @param $id
     * @return Response
     */
    public function article_detail($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $article_detail = $em->getRepository(Article::class)->findOneBy(array('confirm'=>1, 'id'=>$id));

        if (!$article_detail):
            return new JsonResponse("Yazıya ait yazar profili kaldırılmış!");
        endif;

        return $this->render('web_site/pages/article_detail.html.twig', [
            'article_detail' => $article_detail,
        ]);

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
     * @Route({
     *     "en": "/travel-guide/{category}",
     *     "tr": "/gezi-rehberi/{category}"
     * }, name="ptv",  methods={"GET"})
     * @param Request $request
     * @param $category
     * @param PaginatorInterface $paginator
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function placesToVisit(Request $request, $category = null,
                                  PaginatorInterface $paginator, TranslatorInterface $translator): Response
    {
        $id = null;
        $shortName = null;//nature vs.
        $em = $this->getDoctrine()->getManager();

        #kategori translated olarak geldiği için id'sini bulalım
        //tum ptv kategorileriri getir
        $ptvCats = $em->getRepository(PTVCategory::class)->findAll();

        //ptv shortname'sini translate et ve categori ile eşleştirerek id'sini bul
        foreach ($ptvCats as $item => $value) {
            $slug = $this->slugify($translator->trans('header_menu.' . $value->getShortName(), [], 'ptv'));
            if ($slug === $category) {
                $id = $value->getId();
                $shortName = $value->getShortName();
            }
        }

        $params = [
            'type' => $shortName,
            'name' => $request->query->get('title'),
            'place' => $em->getRepository(Place::class)->findOneBy(array('slug' => $request->query->get('place'))),
            'sub_place' => $em->getRepository(Place::class)->findOneBy(array('slug' => $request->query->get('sub_place')))
        ];

        $placesToVisit = $em->getRepository(PlacesToVisit::class)->ptvFilter($params);

        $placesToVisit = $paginator->paginate(
            $placesToVisit,
            $request->query->getInt('page', 1), 5
        );

        return $this->render('web_site/pages/ptv.html.twig', [
            'places_to_visit' => $placesToVisit,
            'short_name' => $shortName,
            'places' => $em->getRepository(Place::class)->getDistricts()
        ]);
    }


    /**
     * Gezilecek Yer - Detay Sayfası
     * @Route({
     *     "en": "{district}/places-to-visit/{slug}",
     *     "tr": "{district}/gezilecek-yerler/{slug}"
     * }, name="ptv_detail",  methods={"GET"})
     * @param $slug
     * @return Response
     */
    public function placesToVisitDetail($district, $slug): Response
    {
        $em = $this->getDoctrine()->getManager();
        $ptv = $em->getRepository(PlacesToVisit::class)->findOneBy(array('slug' => $slug));
        return $this->render('web_site/pages/ptv_detail.html.twig', [
            'ptv' => $ptv,
        ]);
    }

    /**
     * Gezi Rehberi
     * @Route("/ptv-embedded-menu", name="ptv_embedded_menu")
     * header embedded controller
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function ptvEmbeddedMenu(TranslatorInterface $translator): Response
    {


        $em = $this->getDoctrine()->getManager();
        $ptv_category = $em->getRepository(PTVCategory::class)->findBy([], ['sort' => 'ASC']);

        $cat = [];
        foreach ($ptv_category as $key => $value) {

            $ptv = $em->getRepository(PlacesToVisit::class)->count(['type' => $value->getShortName()]);

            $cat[$key]['short_name'] = $translator->trans('header_menu.' . $value->getShortName(), [], 'ptv');
            $cat[$key]['count'] = $ptv;
            $cat[$key]['slug'] = $this->slugify($translator->trans('header_menu.' . $value->getShortName(), [], 'ptv'));

        }

        return $this->render('web_site/embedded_controller/ptv_embedded_menu.html.twig', ['ptv_category' => $cat]);
    }

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


        $params = [

            'name' => $request->query->get('name'),
            'place' => $em->getRepository(Place::class)->findOneBy(array('slug' => $request->query->get('place'))),
            'sub_place' => $em->getRepository(Place::class)->findOneBy(array('slug' => $request->query->get('sub_place')))
        ];

        $events = $em->getRepository(Event::class)->eventFilter($params);

        $events = $paginator->paginate(
            $events,
            $request->query->getInt('page', 1), 5
        );
        return $this->render('web_site/pages/events.html.twig', [
            'events' => $events,
            /*'categories' => $em->getRepository(Event::class)->advertCategoryList(),*/
            'places' => $em->getRepository(Place::class)->getDistricts()
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

        $params = [
            'title' => $request->query->get('title'),
            'cat' => $request->query->get('cat'),
            'place' => $em->getRepository(Place::class)->findOneBy(array('slug' => $request->query->get('place'))),
            'sub_place' => $em->getRepository(Place::class)->findOneBy(array('slug' => $request->query->get('sub_place')))
        ];
        $announces = $em->getRepository(Announce::class)->announceFilter($params);

        $announces = $paginator->paginate(
            $announces,
            $request->query->getInt('page', 1), 5
        );

        $announceCats = array(
            'urgent',
            'lost',
            'discount',
            'death',
            'other'
        );

        return $this->render('web_site/pages/announces.html.twig', [
            'announces' => $announces,
            /*'categories' => $em->getRepository(Event::class)->advertCategoryList(),*/
            'places' => $em->getRepository(Place::class)->getDistricts(),
            'announce_cats' => $announceCats
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

        $params = [
            'name' => $request->query->get('name'),
            'cat' => $em->getRepository(BusinessCategory::class)->findOneBy(array('short_name' => $request->query->get('cat'))),
            'place' => $em->getRepository(Place::class)->findOneBy(array('slug' => $request->query->get('place'))),
            'sub_place' => $em->getRepository(Place::class)->findOneBy(array('slug' => $request->query->get('sub_place')))
        ];

        $businesses = $em->getRepository(Business::class)->businessGuideFilter($params);

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

    /**
     * İlanlar Dinamik Menüsü
     * @Route("/advert_menu", name="advert_menu")
     * header embed controller
     * ilanlar menüsü
     * @param TranslatorInterface $translator
     * @param Request $request
     * @return Response
     */
    public function advertEmbeddedMenu(TranslatorInterface $translator, Request $request): Response
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

        return $this->render('web_site/embedded_controller/advert_menu.html.twig',
            ['ad_categories' => $ad_cats]);
    }





}