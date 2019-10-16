<?php

namespace App\Controller;


use App\Entity\Advert;
use App\Entity\Announce;
use App\Entity\Business;
use App\Entity\Event;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/app")
 */
class AppController extends AbstractController
{

    /**
     * @Route("/dashboard", name="app_dashboard", methods={"GET","POST"})
     */
    public function dashboard()
    {

        $em = $this->getDoctrine()->getManager();


        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {

            $this->denyAccessUnlessGranted('ROLE_ADMIN');

            $adverts = $em->getRepository(Advert::class)
                ->findBy(array('confirm'=>0), array('last_update' => 'DESC', 'confirm' => 'ASC'));

            $events = $em->getRepository(Event::class)
                ->findBy(array('confirm'=>0), array('last_update' => 'DESC', 'confirm' => 'ASC'));

            $announces = $em->getRepository(Announce::class)
                ->findBy(array('confirm'=>0), array('last_update' => 'DESC', 'confirm' => 'ASC'));

            $businesses = $em->getRepository(Business::class)
                ->findBy(array('confirm'=>0), array('last_update' => 'DESC', 'confirm' => 'ASC'));

            $users = $em->getRepository(User::class)->findAll();

            return $this->render('app/dashboard.html.twig', [
                'adverts' => $adverts,
                'events' => $events,
                'announces' => $announces,
                'businesses' => $businesses,
                'users' => $users
            ]);

        }
        else{

            return $this->render('app/user-dashboard.html.twig');


        }






    }

    /**
     * @Route("/test-em", name="test_em", methods={"GET","POST"})
     */
    public function testEM()
    {

        $em = $this->getDoctrine()->getManager();

        /*
        $ptv = new PlacesToVisit();
        $ptv->setAbout("sdlşfk");
        $ptv->setName("lkjlk");
        $em->persist($ptv);
        */

        /*
        $ptv2 = new PlacesToVisit();
        $ptv2->setAbout("222");
        $ptv2->setName("2222");
        $em->persist($ptv2);
        */

        /*
        //eklenecek veriyi gösterir
        echo '<pre>';
        print_r($em->getUnitOfWork()->getScheduledEntityInsertions());
        echo '</pre>';

        //güncellenecek veriyi gösterir
        echo '<pre>';
        print_r($em->getUnitOfWork()->getScheduledEntityUpdates());
        echo '</pre>';

        //silinecek veriyi gösterir
        echo '<pre>';
        print_r($em->getUnitOfWork()->getScheduledEntityDeletions());
        echo '</pre>';
        exit;
        */

        /*
        $ptv2 = new PlacesToVisit();
        $ptv2->setAbout("222");
        $ptv2->setName("2222");
        $em->persist($ptv2);
        */

        //$em->detach($ptv); persist edilecek objeyi çıkarır
        //$em->clear(); tüm işlemleri iptal eder.

        $em->flush();

        return 1;

    }


}
