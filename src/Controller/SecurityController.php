<?php

namespace App\Controller;

use App\Entity\User;
use App\Traits\Util;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use function Couchbase\passthruEncoder;

class SecurityController extends AbstractController
{
    use Util;

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {

        //farklı bir yönlendirme bilgisi var mı?
        $target_page = null;
        $target_path = $request->getSession()->get('_security.main.target_path');

        if ($target_path){
            $target_page = explode('=',$target_path);//business_new, advert_new ...
        }


        $request->getSession()->remove('_security.main.target_path');



        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'from'=>$target_page[1]
        ]);
    }

    /**
     * @Route("/forgot-email", name="forgot_email")
     * @return Response
     */
    public function forgotEmail(): Response
    {
        return $this->render('security/forgot-email.html.twig',
            ['last_username' => "", 'error' => ""]
        );
    }

    /**
     * @Route("/send-reset-link", name="send_reset_link")
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return Response
     * @throws \Exception
     */
    public function sendResetLink(Request $request, \Swift_Mailer $mailer): Response
    {

        //dump($request->request->get('email'));exit;
        $email = $request->request->get('email');

        /**
         * Check valide user
         */
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->findOneBy(array(
            'email' => $email
        ));

        if (!$user):
            $this->addFlash('error', $email . ' adresi sistemde kayıtlı değildir.');
            return $this->redirectToRoute('forgot_email');
        endif;


        /**
         * Sıfırlama linki yakın bir tarihte gönderilmiş mi?
         * Eğer 10 dakikadan daha eski ise yeniden gönderilebilir.
         * Bu kontrolü sürekli istek yapılmaması için yapıyoruz.
         */

        //eğer daha önceden gönderilmiş bir istek varsa kontrole yapıyoruz.
        if ($user->getPasswdResetDueDate()) {

            //modify +10 dk olarak eklenmişti.
            $pass_reset_due_date = $user->getPasswdResetDueDate();
            $now = new \DateTime('now');

            //istek tarihi ile şimdiki tarih arasındaki farkı dk cinsinden hesaplıyoruz
            $date_diff = $this->dateDiffGetInterval(
                $now->format('Y-m-d H:i:s'),
                $pass_reset_due_date->format('Y-m-d H:i:s'),
                'M'
            );


            //henüz 10 dk dolmamışsa
            if ($date_diff > 0) {
                $this->addFlash('error', 'E-posta adresinize az önce yenileme linki gönderildi.
            Bir sonraki istek için ' . round($date_diff) . ' dakika daha beklemelisiniz.');
                return $this->redirectToRoute('forgot_email');
            }
        }


        //todo geçerlilik süresi kontrolü

        /**
         * Reset kodu ve geçerlilik tarihini db'ye kaydet.
         */
        $passwd_reset_code = $this->generateEmailConfirmationCode(32);
        $user->setPasswdResetCode($passwd_reset_code);

        $now = new \DateTime('now');
        $due = $now->modify('+10 minutes');
        //$due->format('d.m.Y H:i:s');

        $user->setPasswdResetDueDate($due);
        $em->flush();


        /**
         * Linki gönder
         */
        $from = array('edremitkorfezi.iletisim@gmail.com' => 'Edremit Körfezi');
        $message = (new \Swift_Message('Parolanızı Sıfırlayın'))
            ->setFrom($from)
            ->setTo($email)
            ->setBody(
                $this->renderView(
                    '_email/password_reset_link.html.twig',
                    array(
                        'email' => $email,
                        'full_name' => $user->getFullName(),
                        'reset_code' => $passwd_reset_code
                    )
                ),
                'text/html'
            );
        $mailer->send($message);


        $this->addFlash('success', 'Sayın ' . $user->getFullName() . ', parola sıfırlama linki 
        e-posta adresinize gönderildi. Lütfen gelen kutunuzu kontrol ediniz.');

        return $this->redirectToRoute('forgot_email');

    }

    /**
     * @Route("/reset-password", name="reset_password")
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return Response
     * @throws \Exception
     */
    public function resetPassword(Request $request, \Swift_Mailer $mailer): Response
    {

        $user_email = $request->query->get('user_email');
        $code = $request->query->get('code');

        /**
         * Check valide user
         */
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->findOneBy(array(
            'email' => $user_email,
            'passwd_reset_code' => $code
        ));

        if (!$user):
            return new JsonResponse('gecersiz istek!', Response::HTTP_BAD_REQUEST);
        endif;

        $full_name = $user->getFullName();

        return $this->render('security/reset-password.html.twig', [
            'full_name' => ' ' . $full_name,
            'code' => $code,
            'user_mail' => $user_email
        ]);
    }

    /**
     * @Route("/update-password", name="update_password")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return JsonResponse|RedirectResponse
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {


        $code = $request->request->get('code');
        $token = $request->request->get('token');
        $password = $request->request->get('password');
        $re_password = $request->request->get('re_password');
        $user_mail = $request->request->get('user_mail');

        if ($password !== $re_password) {
            $this->addFlash('error', 'Girdiğiniz parolalar uyuşmamaktadır. Lütfen tekrar deneyiniz.');
            return $this->redirectToRoute('reset_password', [
                'code' => $code,
                'user_email' => $user_mail
            ]);
        }

        if (strlen($password) < 6) {
            $this->addFlash('error', 'Parolanız en az 6 karakterden oluşmalıdır.');
            return $this->redirectToRoute('reset_password', [
                'code' => $code,
                'user_email' => $user_mail
            ]);
        }



        /**
         * Check valide user
         */
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->findOneBy(array(
            'email' => $user_mail,
            'passwd_reset_code' => $code
        ));

        if (!$user):
            return new JsonResponse('gecersiz istek!', Response::HTTP_BAD_REQUEST);
        endif;


        // encode the plain password
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $password
            )
        );

        $user->setPasswdResetDueDate(null);
        $user->setPasswdResetCode(null);

        $em->flush();


        $this->addFlash('success', 'Parolanız başarılı bir şekilde güncellendi. Yeni parolanızla giriş yapabilirsiniz.');
        return $this->redirectToRoute('app_login');

    }

    /**
     * @Route("/logout", name="app_logout")
     * @throws \Exception
     */
    public function logout(): void
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @param Request $request
     * @Route("/confirm-email", name="confirm_email")
     * @return JsonResponse|RedirectResponse
     */
    public function confirmEmail(Request $request)
    {


        $user_email = $request->query->get('user_email');
        $confirmation_code = $request->query->get('code');

        /**
         * Check validate user
         * @var User[] $check_user
         */
        $em = $this->getDoctrine()->getManager();


        $check_user = $em->getRepository(User::class)->findOneBy(
            array(
                'email' => $user_email,
                'confirmation_code' => $confirmation_code
            )
        );

        if ($check_user) {
            $check_user->setConfirmed(1);
            $check_user->setConfirmationCode(null);

            $em->flush();

            $this->addFlash('success', 'Hesabınız başarılı bir şekilde doğrulandı.');

            return $this->redirectToRoute('app_dashboard');

        } else {
            return new JsonResponse('bad request', Response::HTTP_BAD_REQUEST);
        }


    }
}
