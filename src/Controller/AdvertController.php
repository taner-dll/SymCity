<?php

namespace App\Controller;


use App\Entity\AdSubCategory;
use App\Entity\Advert;
use App\Entity\Place;
use App\Form\AdvertType;
use App\Repository\AdvertRepository;
use App\Traits\File;
use App\Traits\Util;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/advert")
 */
class AdvertController extends AbstractController
{
    use File;
    use Util;

    const CONFIRM = 1;
    const SAVE_AS_DRAFT = 2;
    const SEND_CONFIRMATION_REQUEST = 0;

    /**
     * @Route("/", name="advert_index", methods={"GET"})
     * @param AdvertRepository $advertRepository
     * @return Response
     */
    public function index(AdvertRepository $advertRepository): Response
    {

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $adverts = $advertRepository->findAll();
        } else {
            $adverts = $advertRepository->findBy(array('user' => $this->getUser()));
        }

        return $this->render('advert/index.html.twig', [
            'adverts' => $adverts,
        ]);
    }

    /**
     * @Route("/new", name="advert_new", methods={"GET","POST"})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {


        /*dump($request->request->all());
        exit;*/

        $advert = new Advert();
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);


        /*dump($form);exit;*/

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();


            $sub_category = $request->request->get('sub_cat');
            $sub_place = $request->request->get('sub_place');

            if ($sub_category):
                $advert->setSubCategory($entityManager->find(AdSubCategory::class, $sub_category));
            endif;


            if ($sub_place):
                $advert->setSubPlace($entityManager->find(Place::class, $sub_place));
            endif;


            //url slug
            $advert->setSlug($this->slugify($request->request->get('advert')['title']));

            $advert->setUser($this->getUser());
            $advert->setLastUpdate(new DateTime('now'));
            $advert->setConfirm(self::SAVE_AS_DRAFT);

            $entityManager->persist($advert);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('advert_added'));
            //jquery-cropper (cropped image hidden input)
            $file = $request->request->get('cropped_image');

            //cropped image
            if (!empty($file)) {

                //dosya adı oluştur ve db güncelle
                $fileName = $this->base64generateFileName($file);
                $advert->setFeaturedImage($fileName);
                $entityManager->flush();

                //dosya yükle
                $dir = $this->getParameter('adv_directory');
                $this->base64upload($file, $dir, $fileName);
            }

            return $this->redirectToRoute('advert_show', array('id' => $advert->getId()));
        }

        return $this->render('advert/new.html.twig', [
            'advert' => $advert,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="advert_show", methods={"GET"})
     * @param Advert $advert
     * @return Response
     */
    public function show(Advert $advert): Response
    {

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') &&
            $advert->getUser()->getId() !=
            $this->getUser()->getId()) {
            return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
        }

        return $this->render('advert/show.html.twig', [
            'advert' => $advert,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="advert_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Advert $advert
     * @param TranslatorInterface $translator
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, Advert $advert, TranslatorInterface $translator): Response
    {

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            if ($advert->getUser()->getId() != $this->getUser()->getId()) {
                return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
            }
        }

        //eski resmi kaldırırken sorgu gerekti. product->getPicture() temp olarak gözüküyor?
        $em = $this->getDoctrine()->getManager();
        $p = $em->getRepository(Advert::class)->find($advert->getId());
        $fileOldName = $p->getFeaturedImage();

        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);



        //dump($request->request->all());exit;
        //dump($form);exit;
        if ($form->isSubmitted()/* && $form->isValid()*/) {

            $em = $this->getDoctrine()->getManager();

            $advert->setFeaturedImage($advert->getFeaturedImage());
            $advert->setConfirm(2);//guncellenince taslak olarak kaydedilmeli
            $advert->setLastUpdate(new DateTime('now'));

            //url slug
            $advert->setSlug($this->slugify($advert->getTitle()));


            //dump($request->request->all());exit;

            if (isset($request->request->get('advert')['sub_category'])):
                $sub_cat_short_name = $request->request->get('advert')['sub_category'];
                $sub_cat = $em->getRepository(AdSubCategory::class)->findOneBy(
                    ['short_name'=>$sub_cat_short_name]);

                $advert->setSubCategory($sub_cat);
            endif;

            if (isset($request->request->get('advert')['sub_place'])):
                $sub_place_id = $request->request->get('advert')['sub_place'];
                $advert->setSubPlace($em->find(Place::class, $sub_place_id));
            endif;

            //money unformat to float 55.000,70 -> 55000.70
            if (isset($request->request->get('advert')['price'])):
                $money = $request->request->get('advert')['price'];
                $money = str_replace('.','',$money);
                $money = str_replace(',','.',$money);
                $advert->setPrice($money);
            endif;



            $em->flush();
            $this->addFlash('success', $translator->trans('advert_updated'));


            //jquery-cropper (cropped image hidden input)
            $file = $request->request->get('cropped_image');

            //dump($file);exit;


            //cropped image
            if (!empty($file)) {

                //dosya adı oluştur ve db güncelle
                $fileName = $this->base64generateFileName($file);
                //dump($fileName);exit;

                $advert->setFeaturedImage($fileName);
                $em->flush();

                //dosya yükle
                $dir = $this->getParameter('adv_directory');
                $this->base64update($file, $dir, $fileName, $fileOldName);

            }

            return $this->redirectToRoute('advert_show', [
                'id' => $advert->getId(),
            ]);
        }

        return $this->render('advert/edit.html.twig', [
            'advert' => $advert,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="advert_delete", methods={"DELETE"})
     * @param Request $request
     * @param Advert $advert
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Request $request, Advert $advert, TranslatorInterface $translator): Response
    {

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            if ($advert->getUser()->getId() != $this->getUser()->getId()) {
                return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
            }
        }

        if ($this->isCsrfTokenValid('delete' . $advert->getId(), $request->request->get('_token'))) {

            //öne çıkan resmi sil
            $dir = $this->getParameter('adv_directory');
            $this->deleteFile($dir, $advert->getFeaturedImage());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($advert);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('advert_deleted'));
        }

        return $this->redirectToRoute('advert_index');
    }

    /**
     * @Route("/featured/photo/delete/{advert}", name="advert_featured_photo_delete", methods={"GET"})
     * @param Request $request
     * @param $advert
     * @param TranslatorInterface $translator
     * @return mixed
     */
    public function deleteFeatured(Request $request, $advert, TranslatorInterface $translator): Response
    {


        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('delete-featured-photo' . $advert, $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $photo = $em->getRepository(Advert::class)->find($advert);

            if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                if ($photo->getUser()->getId() != $this->getUser()->getId()) {
                    return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
                }
            }

            $dir = $this->getParameter('adv_directory');
            $this->deleteFile($dir, $photo->getFeaturedImage());

            $photo->setFeaturedImage(null);
            $em->flush();

            $this->addFlash('success', $translator->trans('advert_featured_image_deleted'));

        }

        return $this->redirectToRoute('advert_show', ['id' => $advert]);

    }

    /**
     * @Route("/confirm/{id}", name="advert_confirm", methods={"GET"})
     * @param Request $request
     * @param $id
     * @param TranslatorInterface $translator
     * @param \Swift_Mailer $mailer
     * @return mixed
     */
    public function confirm(Request $request, $id, TranslatorInterface $translator,
                            \Swift_Mailer $mailer): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('confirm' . $id, $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $advert = $em->getRepository(Advert::class)->find($id);
            $advert->setConfirm(self::CONFIRM);
            $em->flush();

            //yayına alındığına dair e-posta gönderimi
            $from = array('edremitkorfezi.iletisim@gmail.com' => 'Edremit Körfezi');
            $message = (new \Swift_Message('İlanınız Yayında!'))
                ->setFrom($from)
                ->setTo($advert->getUser()->getEmail())
                ->setBody(
                    $this->renderView(
                        '_email/advert_confirmed.html.twig',
                        array(
                            'baslik' => $advert->getTitle()
                        )
                    ),
                    'text/html'
                );
            $mailer->send($message);

            $this->addFlash('success', $translator->trans('advert_confirmed'));

        }

        return $this->redirectToRoute('advert_show', ['id' => $id]);

    }

    /**
     * @Route("/unconfirm/{id}", name="advert_unconfirm", methods={"GET"})
     * @param Request $request
     * @param $id
     * @param TranslatorInterface $translator
     * @return mixed
     */
    public function unconfirm(Request $request, $id, TranslatorInterface $translator): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('unconfirm' . $id, $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $advert = $em->getRepository(Advert::class)->find($id);
            $advert->setConfirm(self::SAVE_AS_DRAFT);
            $em->flush();

            //todo yayından kaldırılma sebebi mail olarak gönderilebilir.

            $this->addFlash('success', $translator->trans('advert_unconfirmed'));

        }

        return $this->redirectToRoute('advert_show', ['id' => $id]);

    }

    /**
     * @Route("/save-as-draft/{id}", name="advert_save_as_draft", methods={"GET"})
     * @param Request $request
     * @param $id
     * @param TranslatorInterface $translator
     * @return mixed
     */
    public function saveAsDraft(Request $request, $id, TranslatorInterface $translator): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('advert_save_as_draft' . $id, $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $advert = $em->getRepository(Advert::class)->find($id);
            $advert->setConfirm(self::SAVE_AS_DRAFT);
            $em->flush();

            $this->addFlash('success', $translator->trans('advert_confirm_cancelled'));
        }

        return $this->redirectToRoute('advert_show', ['id' => $id]);

    }

    /**
     * @Route("/send-confirmation-request/{id}", name="advert_send_confirmation_request", methods={"GET"})
     * @param Request $request
     * @param $id
     * @param TranslatorInterface $translator
     * @return mixed
     */
    public function sendConfirmationRequest(Request $request, $id, TranslatorInterface $translator): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('advert_send_confirmation_request' . $id, $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $advert = $em->getRepository(Advert::class)->find($id);
            $advert->setConfirm(self::SEND_CONFIRMATION_REQUEST);
            $em->flush();

            $this->addFlash('success', $translator->trans('advert_send_confirmation_request'));
        }

        return $this->redirectToRoute('advert_show', ['id' => $id]);

    }

}