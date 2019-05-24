<?php

namespace App\Controller;

use App\Traits\Util;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\PlacesToVisit;

class WebSiteController extends AbstractController
{

    use Util;
    /**
     * @Route("/", name="index")
     */
     public function index(){

        return $this->render('web_site/base.html.twig');
    }

    /**
     * @Route("/places-to-visit/{slug}", name="ptv_page",  methods={"GET"})
     * @param $slug
     * @return Response
     */
    public function ptv_page($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $ptv = $em->getRepository(PlacesToVisit::class)->findOneBy(array('slug'=>$slug));

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
        $ptv = $em->getRepository(PlacesToVisit::class)->findBy(array(), array('name'=>'ASC'));
        return $this->render('web_site/embedded_controller/menu1.html.twig', ['ptv'=>$ptv]);
     }
}
