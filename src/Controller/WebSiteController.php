<?php

namespace App\Controller;

use App\Entity\Business;
use App\Traits\Util;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;
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
     * @Route("/business-guide", name="business_guide",  methods={"GET"})
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function business_guide(Request $request, PaginatorInterface $paginator)
    {

        $em = $this->getDoctrine()->getManager();
        $businesses = $em->getRepository(Business::class)->findBy(array('confirm' => 1));


        // Paginate the results of the query
        $businesses = $paginator->paginate(
        // Doctrine Query, not results
            $businesses,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            20
        );

        return $this->render('web_site/pages/business_guide.html.twig', [
            'businesses' => $businesses,
        ]);

    }


    /**
     * @Route("/business-detail/{id}/{slug}", name="business_detail",  methods={"GET"})
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param $id
     * @return Response
     */
    public function business_detail(Request $request, PaginatorInterface $paginator, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $businesses_detail = $em->getRepository(Business::class)->findOneBy(
            array('confirm' => 1, 'id' => $id));

        return $this->render('web_site/pages/business_detail.html.twig', [
            'business_detail' => $businesses_detail,
        ]);

    }


    /**
     * @Route("/places-to-visit/{slug}", name="ptv_page",  methods={"GET"})
     * @param $slug
     * @return Response
     */
    public function ptv_page($slug)
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
    public function menu1()
    {
        $em = $this->getDoctrine()->getManager();
        $ptv = $em->getRepository(PlacesToVisit::class)->findBy(array(), array('name' => 'ASC'));
        return $this->render('web_site/embedded_controller/menu1.html.twig', ['ptv' => $ptv]);
    }


    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $business = $em->getRepository(Business::class)
            ->findBy(array('confirm' => 1));

        /**
         * Hangi işletmeden kaç adet olduğunu hesaplıyoruz.
         */
        $business_count = array();
        $eat = 0;
        $cafe = 0;
        $hotel = 0;
        $hostel = 0;
        $camp = 0;
        $tour_travel = 0;
        $health_beauty = 0;
        $car = 0;
        $repair_service = 0;
        $taxi = 0;
        $law = 0;
        $insurance = 0;
        $real_estate = 0;
        $market = 0;
        $shop = 0;
        $other = 0;

        foreach ($business as $b) {
            switch ($b->getType()) {
                case 'eat':
                    $eat++;
                    break;
                case 'cafe':
                    $cafe++;
                    break;
                case 'hotel':
                    $hotel++;
                    break;
                case 'hostel':
                    $hostel++;
                    break;
                case 'camp':
                    $camp++;
                    break;
                case 'tour-travel':
                    $tour_travel++;
                    break;
                case 'health-beauty':
                    $health_beauty++;
                    break;
                case 'car':
                    $car++;
                    break;
                case 'repair-service':
                    $repair_service++;
                    break;
                case 'taxi':
                    $taxi++;
                    break;
                case 'law':
                    $law++;
                    break;
                case 'insurance':
                    $insurance++;
                    break;
                case 'real-estate':
                    $real_estate++;
                    break;
                case 'market':
                    $market++;
                    break;
                case 'shop':
                    $shop++;
                    break;
                case 'other':
                    $other++;
                    break;
            }
        }

        $business_count['eat'] = $eat;
        $business_count['cafe'] = $cafe;
        $business_count['hotel'] = $hotel;
        $business_count['hostel'] = $hostel;

        $business_count['camp'] = $camp;
        $business_count['tour'] = $tour_travel;
        $business_count['health'] = $health_beauty;
        $business_count['car'] = $car;

        $business_count['repair'] = $repair_service;
        $business_count['taxi'] = $taxi;
        $business_count['law'] = $law;
        $business_count['insurance'] = $insurance;

        $business_count['realestate'] = $real_estate;
        $business_count['market'] = $market;
        $business_count['shop'] = $shop;
        $business_count['other'] = $other;

        return $this->render('web_site/base.html.twig',
            array(
                'business_count' => $business_count,
            ));
    }
}
