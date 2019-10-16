<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class Test1Controller extends AbstractController
{
    /**
     * @Route("/test1", name="test1")
     */
    public function index()
    {
        return $this->render('test1/index.html.twig', [
            'controller_name' => 'Test1Controller',
        ]);
    }
}
