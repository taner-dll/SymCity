<?php

namespace App\Controller;

use App\Entity\AdCategory;
use App\Entity\Advert;
use App\Entity\Announce;
use App\Entity\Business;
use App\Entity\Event;
use App\Entity\Place;
use App\Traits\Util;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\PlacesToVisit;

class WebSiteController extends AbstractController
{

    use Util;

    /**
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
            'places' => $em->getRepository(Place::class)->findAll()
        ]);
    }

    /**
     * @Route({
     *     "en": "/advert-detail/{id}/{slug}",
     *     "tr": "/ilan-detay/{id}/{slug}"
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
            'places' => $em->getRepository(Place::class)->findAll()
        ]);
    }

    /**
     * @Route({
     *     "en": "/business-detail/{id}/{slug}",
     *     "tr": "/isletme-detay/{id}/{slug}"
     * }, name="business_detail",  methods={"GET"})
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param $id
     * @return Response
     */
    public function business_detail(Request $request, PaginatorInterface $paginator, $id): Response
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
     * @Route("/places-to-visit/{slug}", name="ptv_page",  methods={"GET"})
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


    /**
     * @Route("/menu1", name="menu1")
     * header embed controller
     */
    public function menu1(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $ptv = $em->getRepository(PlacesToVisit::class)->findBy(array(), array('name' => 'ASC'));
        return $this->render('web_site/embedded_controller/menu1.html.twig', ['ptv' => $ptv]);
    }

    /**
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


    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $business = $em->getRepository(Business::class)
            ->findBy(array('confirm' => 1));

        return $this->render('web_site/base.html.twig',
            array(
                'business_count' => $business,
            ));
    }
}
