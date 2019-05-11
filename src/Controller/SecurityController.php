<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\UserType;
use http\Env\Response;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * @Route("/security")
 */
class SecurityController extends AbstractController
{

    /**
     * @Route("/", name="index")
     */
    public function index(){
        

        return $this->render('web_site/base.html.twig');
    }


    /**
     * @Route("/list-users", name="list_users")
     */
    public function listUsers(){

        $em = $this->getDoctrine()->getManager();

        $users = $em->createQueryBuilder()
            ->select('u')
            ->from('App:User','u')
            ->getQuery()
            ->getArrayResult();

        return new JsonResponse($users);

    }


    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request){

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();


        //her girişte currency euro olmalı. sistem euro üzerine kurulu
        $session = $this->get('session');
        $session->remove('currency');


        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error
        ));
    }


    /**
     * @Route("/register", name="register")
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(UserPasswordEncoderInterface $passwordEncoder, Request $request){

        #Üye kaydı yapmıyoruz.


        /*
        $response = new \Symfony\Component\HttpFoundation\Response();
        return new JsonResponse("kayit yapilamaz", $response::HTTP_FORBIDDEN);

        */

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setUsername($user->getEmail());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            //return $this->redirectToRoute('login');

            #automatic login after registration
            $token = new UsernamePasswordToken(
                $user,$user->getPlainPassword(), 'main', $user->getRoles()
            );

            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main',serialize($token));

            // Fire the login event manually
            $event = new InteractiveLoginEvent($request, $token);

            $dispatcher = new EventDispatcher();
            $dispatcher->dispatch('security.interactive_login',$event);


            return $this->redirectToRoute('app_dashboard');

        }

        return $this->render('security/register.html.twig', array(
            'form' => $form->createView()
        ));

    }


}
