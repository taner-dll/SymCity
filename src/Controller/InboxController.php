<?php

namespace App\Controller;

use App\Entity\Inbox;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/inbox")
 */
class InboxController extends AbstractController
{

    /**
     * @Route({
     *     "en": "/send-message",
     *     "tr": "/mesaj-gonder"
     * }, name="business_detail",  methods={"POST"})
     *
     * @return Response
     */
    public function sendMessage(Request $request){

        $em = $this->getDoctrine()->getManager();

        $msg_to =  $request->request->get('msg_to');
        $userTo = $em->find(User::class, $msg_to);



        $msg = $request->request->get('msg');



        $inbox = new Inbox();
        $inbox->setCreatedAt(new \DateTime('now'));
        $inbox->setMessage($msg);
        $inbox->setUser($this->getUser());
        $inbox->setUserFrom($this->getUser());
        $inbox->setUserTo($userTo);
        $em->persist($inbox);

        $inbox2 = new Inbox();
        $inbox2->setCreatedAt(new \DateTime('now'));
        $inbox2->setMessage($msg);
        $inbox2->setUser($this->getUser());
        $inbox2->setUserFrom($this->getUser());
        $inbox2->setUserTo($userTo);
        $em->persist($inbox);

        //TODO Inbox type field: sent, incoming
        //TODO ilan detay mesaj gönder ajax post
        $em->flush();

        return new JsonResponse(Response::HTTP_OK);


    }
}
