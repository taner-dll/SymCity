<?php

namespace App\Controller;

use App\Entity\User;
use App\Traits\Util;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use function Couchbase\passthruEncoder;

class SecurityController extends AbstractController
{
    use Util;
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/forgot-email", name="forgot_email")
     * @return Response
     */
    public function forgotEmail(): Response
    {
        return $this->render('security/forgot-email.html.twig', ['last_username' => "", 'error' => ""]);
    }

    /**
     * @Route("/logout", name="app_logout")
     * @throws \Exception
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @param Request $request
     * @Route("/confirm-email", name="confirm_email")
     * @return JsonResponse|RedirectResponse
     */
    public function confirmEmail(Request $request){


        $user_email=$request->query->get('user_email');
        $confirmation_code=$request->query->get('code');

        /**
         * Check validate user
         * @var User[] $check_user
         */
        $em = $this->getDoctrine()->getManager();


        $check_user = $em->getRepository(User::class)->findOneBy(
            array(
                'email'=>$user_email,
                'confirmation_code'=>$confirmation_code
            )
        );

        if ($check_user){
            $check_user->setConfirmed(1);
            $check_user->setConfirmationCode(null);

            $em->flush();

            $this->addFlash('success','Hesabınız başarılı bir şekilde doğrulandı.');

            return $this->redirectToRoute('app_dashboard');

        }
        else{
            return new JsonResponse('bad request', Response::HTTP_BAD_REQUEST);
        }



    }
}
