<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @param \Swift_Mailer $mailer
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @return Response
     * @throws \Exception
     */
    public function register(\Swift_Mailer $mailer,
                             Request $request, UserPasswordEncoderInterface $passwordEncoder,
                             GuardAuthenticatorHandler $guardHandler,
                             LoginFormAuthenticator $authenticator): Response
    {


        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);


        /**
         * Check valide user
         */
        $em = $this->getDoctrine()->getManager();

        $check_user = $em->getRepository(User::class)->findOneBy(array(
            'email' => $request->request->get('registration_form')['email']
        ));

        if ($check_user):
            $form->addError(new FormError('Kayıtlı bir e-posta adresi girdiniz.'));
        endif;


        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setLastLogin(new \DateTime('now'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            //yayına alındığına dair e-posta gönderimi
            $from = array('edremitkorfezi.iletisim@gmail.com' => 'Edremit Körfezi');
            $message = (new \Swift_Message('Aramıza Hoş Geldiniz!'))
                ->setFrom($from)
                ->setTo($request->request->get('registration_form')['email'])
                ->setBody(
                    $this->renderView(
                        '_email/thank_you_new_user.html.twig',
                        array(
                            'email'=>$request->request->get('registration_form')['email']
                        )
                    ),
                    'text/html'
                );
            $mailer->send($message);

            $this->addFlash('success','Sayın '.$user->getFullName().', aramıza hoş geldiniz!');


            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/check-email", name="check_email", methods={"POST"}, options={"expose"=true})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function checkEmail(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $req = $request->request->get('email');
        $user = $em->getRepository(User::class)->findOneBy(array('email' => $req));

        if (!$user) {
            return new JsonResponse(0);
        } else {
            return new JsonResponse(1);
        }


    }

    /**
     * @Route("/check-username", name="check_username", methods={"POST"}, options={"expose"=true})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return void
     */
    public function checkUsername(Request $request, TranslatorInterface $translator)
    {

        //todo username aktif olduğu zaman yapılacak.


    }
}
