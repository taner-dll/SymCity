<?php

namespace App\Controller;


use App\Entity\Place;
use App\Entity\User;
use App\Form\PlaceType;
use App\Repository\PlaceRepository;
use App\Traits\File;
use App\Traits\Util;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{

    use File;
    use Util;


    /**
     * @Route("/update-info", name="user_update_info", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function updateInfo(Request $request): Response
    {

        $em = $this->getDoctrine()->getManager();

        $user = $em->find(User::class,$this->getUser()->getId());

        $user->setFirstname($request->request->get('firstname'));
        $user->setLastname($request->request->get('lastname'));

        $user->setBirthday(\DateTime::createFromFormat('d/m/Y',$request->request->get('birthday')));
        $user->setGender($request->request->get('gender'));

        $user->setGsm($request->request->get('gsm'));

        $em->flush();

        $this->addFlash('success','Bilgileriniz başarılı bir şekilde güncellendi');

        return $this->redirect($this->generateUrl('app_dashboard'));

    }


}
