<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Test1Controller extends AbstractController
{
    /**
     * @Route("/test1", name="test1")
     */
    public function index()
    {
        return new JsonResponse("sdfsdf");
    }
}
