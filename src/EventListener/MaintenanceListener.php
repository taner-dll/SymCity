<?php
// src/EventListener/MaintenanceListener.php

namespace App\EventListener;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class MaintenanceListener
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(RequestEvent $event)
    {


        // get maintenance parameters
        $underMaintenanceUntil = $this->container->hasParameter('underMaintenanceUntil') ?
            $this->container->getParameter('underMaintenanceUntil') : false;

        $maintenance = $this->container->hasParameter('maintenance') ?
            $this->container->getParameter('maintenance') : null;

        $debug = in_array($this->container->get('kernel')->getEnvironment(), array('test'));

        if ($maintenance && !$debug && !$this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {

            $engine = $this->container->get('twig');

            $content = $engine->render('_maintenance/maintenance.html.twig',
                array('underMaintenanceUntil' => $underMaintenanceUntil));

            $event->setResponse(new Response($content, 503));

        }

    }
}