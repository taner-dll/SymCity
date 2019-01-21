<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WebSiteController extends AbstractController
{
    /**
     * @Route("/web/site", name="web_site")
     */
    public function index()
    {
        return $this->render('web_site/index.html.twig', [
            'controller_name' => 'WebSiteController',
        ]);
    }
}
