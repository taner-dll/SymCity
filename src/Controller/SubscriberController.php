<?php

namespace App\Controller;


use App\Entity\Subscriber;
use App\Entity\User;
use App\Traits\Util;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class SubscriberController extends AbstractController
{

    use Util;


    /**
     * Abonelere Ekle
     * @Route({
     *     "en": "/add-subscriber",
     *     "tr": "/abone-ekle"
     * }, name="add_subscriber",  methods={"POST"})
     * @param Request $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function add(Request $request)
    {


        $submitted_token = $request->request->get('subscribe_token');

        if ($this->isCsrfTokenValid('subscribe-item', $submitted_token)) {
            $subscribe_email = $request->request->get('subscribe_email');


            $em = $this->getDoctrine()->getManager();

            echo $subscribe_email;

            $check_subscriber = $em->getRepository(Subscriber::class)
                ->findOneBy(array('email' => $subscribe_email));

            if ($check_subscriber) {

                $this->addFlash('warning', 'Girilen e-posta zaten aboneler arasındadır.');
                return $this->redirect($this->generateUrl('index'));
            }


            $check_user = $em->getRepository(User::class)
                ->findOneBy(array('email' => $subscribe_email));

            if ($check_user) {

                $this->addFlash('warning', 'Girilen e-posta sistemde kayıtlıdır.');
                return $this->redirect($this->generateUrl('index'));

            }

            $subscriber = new Subscriber();
            $subscriber->setEmail($subscribe_email);
            $subscriber->setCreatedAt(new \DateTime('now'));
            $em->persist($subscriber);

            $em->flush();

            $this->addFlash('success', 'Girmiş olduğunuz e-posta adresi, 
        bültenimize başarıyla kaydedildi.');
            return $this->redirect($this->generateUrl('index'));
        } else {
            $this->addFlash('warning', 'Geçersiz CSRF token...');
            return $this->redirect($this->generateUrl('index'));
        }


    }


}
