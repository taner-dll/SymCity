<?php

namespace App\Controller;

use App\Entity\User;
use App\Traits\File;
use App\Traits\Util;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    use File;
    use Util;

    /**
     * @Route("/update-info", name="user_update_info", methods={"POST"}, options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function updateInfo(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        //dump($request->request->all());
        $user = $em->find(User::class, $this->getUser()->getId());
        $user->setUserName($request->request->get('username'));
        $user->setFirstname($request->request->get('firstname'));
        $user->setLastname($request->request->get('lastname'));
        $user->setBirthday(
            \DateTime::createFromFormat('d/m/Y',
                $request->request->get('birthday')
            ));
        $user->setGender($request->request->get('gender'));
        $user->setGsm($request->request->get('gsm'));
        $em->flush();
        //$this->addFlash('success', 'Bilgileriniz başarılı bir şekilde güncellendi');
        //return $this->redirect($this->generateUrl('app_dashboard'));
        return new JsonResponse('user info has been successfully updated', Response::HTTP_OK);
    }

    /**
     * @Route("/update-password", name="user_update_password", methods={"POST"}, options={"expose"=true})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->find(User::class, $this->getUser()->getId());
        // encode the plain password
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $request->request->get('password')
            )
        );
        $em->flush();
        return new JsonResponse('password has been successfully updated.', Response::HTTP_OK);
    }

    /**
     * @Route("/check-username", name="user_check_username", methods={"GET"}, options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function checkUsername(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $username = $request->query->get('username');
        $check_username = $em->getRepository(User::class)
            ->findUsernameByNotCurrentUser($username,$this->getUser()->getId());
        if (count($check_username)>0){
            return  new JsonResponse(count($check_username), Response::HTTP_FOUND);
        }
        else{
            return  new JsonResponse('username is available', Response::HTTP_OK);
        }
    }

    /**
     * @Route("/update-profile-picture", name="user_update_profile_picture", methods={"POST"}, options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function updateProfilePicture(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->find(User::class, $this->getUser()->getId());
        //jquery-cropper (cropped image hidden input)
        $file = $request->request->get('cropped_image');
        //cropped image
        if (!empty($file)) {
            //dosya adı oluştur ve db güncelle
            $fileName = $this->base64generateFileName($file);
            $user->setPicture($fileName);
            $em->flush();
            //dosya yükle
            $dir = $this->getParameter('upc_directory');
            $this->base64upload($file, $dir, $fileName);
            $this->addFlash('success', 'Profil resmi başarılı bir şekilde değiştirildi.');
        }
        return $this->redirectToRoute('app_dashboard');
    }

    /**
     * @Route("/delete-profile-picture", name="delete_profile_picture", methods={"GET"})
     * @param Request $request
     * @return mixed
     */
    public function deleteProfilePicture(Request $request): Response
    {
        $submittedToken = $request->query->get('_token');
        if ($this->isCsrfTokenValid('delete-profile-picture' . $request->query->get('picture'), $submittedToken)) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->find(User::class, $this->getUser()->getId());
            $dir = $this->getParameter('upc_directory');
            $this->deleteFile($dir, $request->query->get('picture'));
            $user->setPicture(null);
            $em->flush();
            $this->addFlash('success', "Profil resmi başarılı bir şekilde silindi.");
        }
        return $this->redirectToRoute('app_dashboard');
    }
}
