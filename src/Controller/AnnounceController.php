<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Entity\Event;
use App\Form\AnnounceType;
use App\Repository\AnnounceRepository;
use App\Traits\File;
use App\Traits\Util;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/announce")
 */
class AnnounceController extends AbstractController
{

    use File;
    use Util;

    const CONFIRM = 1;
    const SAVE_AS_DRAFT = 2;
    const SEND_CONFIRMATION_REQUEST = 0;

    /**
     * @Route("/", name="announce_index", methods={"GET"})
     * @param AnnounceRepository $announceRepository
     * @return Response
     */
    public function index(AnnounceRepository $announceRepository): Response
    {

        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $announces = $announceRepository->findAll();
        }
        else{
            $announces = $announceRepository->findBy(array('user'=>$this->getUser()));
        }

        return $this->render('announce/index.html.twig', [
            'announces' => $announces,
        ]);
    }

    /**
     * @Route("/new", name="announce_new", methods={"GET","POST"})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     * @throws \Exception
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $announce = new Announce();
        $form = $this->createForm(AnnounceType::class, $announce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            //url slug
            $announce->setSlug($this->slugify($request->request->get('announce')['name']));

            $announce->setUser($this->getUser());
            $announce->setLastUpdate(new DateTime('now'));
            $entityManager->persist($announce);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('announce_added'));


            //jquery-cropper (cropped image hidden input)
            $file = $request->request->get('cropped_image');

            //cropped image
            if(!empty($file)) {

                //dosya adı oluştur ve db güncelle
                $fileName = $this->base64generateFileName($file);
                $announce->setImage($fileName);
                $entityManager->flush();

                //dosya yükle
                $dir = $this->getParameter('ann_directory');
                $this->base64upload($file, $dir, $fileName);
            }



            return $this->redirectToRoute('announce_index');
        }

        return $this->render('announce/new.html.twig', [
            'announce' => $announce,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="announce_show", methods={"GET"})
     * @param Announce $announce
     * @return Response
     */
    public function show(Announce $announce): Response
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            if($announce->getUser()->getId() != $this->getUser()->getId()){
                return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
            }
        }
        
        return $this->render('announce/show.html.twig', [
            'announce' => $announce,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="announce_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Announce $announce
     * @param TranslatorInterface $translator
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request, Announce $announce, TranslatorInterface $translator): Response
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            if($announce->getUser()->getId() != $this->getUser()->getId()){
                return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
            }
        }

        //eski resmi kaldırırken sorgu gerekti. product->getPicture() temp olarak gözüküyor?
        $em = $this->getDoctrine()->getManager();   
        $p = $em->getRepository(Announce::class)->find($announce->getId());
        $fileOldName = $p->getImage();

        $form = $this->createForm(AnnounceType::class, $announce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $announce->setImage($fileOldName);
            $announce->setConfirm(0);//announce guncellenince onay yeniden 0 olmalı.
            $announce->setLastUpdate(new DateTime('now'));

            //url slug
            $announce->setSlug($this->slugify($announce->getName()));

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $translator->trans('announce_updated'));


            //jquery-cropper (cropped image hidden input)
            $file = $request->request->get('cropped_image');


            //cropped image
            if(!empty($file)) {

                //dosya adı oluştur ve db güncelle
                $fileName = $this->base64generateFileName($file);
                $announce->setImage($fileName);
                $em->flush();

                //dosya yükle
                $dir = $this->getParameter('ann_directory');
                $this->base64update($file, $dir, $fileName, $fileOldName);

            }

            return $this->redirectToRoute('announce_show', [
                'id' => $announce->getId(),
            ]);
        }

        return $this->render('announce/edit.html.twig', [
            'announce' => $announce,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="announce_delete", methods={"DELETE"})
     * @param Request $request
     * @param Announce $announce
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Request $request, Announce $announce, TranslatorInterface $translator): Response
    {

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            if($announce->getUser()->getId() != $this->getUser()->getId()){
                return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
            }
        }

        if ($this->isCsrfTokenValid('delete'.$announce->getId(), $request->request->get('_token'))) {

            //öne çıkan resmi sil
            $dir = $this->getParameter('ann_directory');
            $this->deleteFile($dir, $announce->getImage());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($announce);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('announce_deleted'));
        }

        return $this->redirectToRoute('announce_index');
    }

    /**
     * @Route("/announce/featured/photo/delete/{announce}", name="announce_featured_photo_delete", methods={"GET"})
     * @param Request $request
     * @param $announce
     * @param TranslatorInterface $translator
     * @return mixed
     */
    public function deleteFeatured(Request $request,$announce, TranslatorInterface $translator): Response
    {
        
        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('delete-featured-photo'.$announce  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $photo = $em->getRepository(Announce::class)->find($announce);

            if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                if($photo->getUser()->getId() != $this->getUser()->getId()){
                    return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
                }
            }

            $dir = $this->getParameter('ann_directory');
            $this->deleteFile($dir,$photo->getImage());

            $photo->setImage(null);
            $em->flush();

            $this->addFlash('success', $translator->trans('announce_featured_image_deleted'));

        }

        return $this->redirectToRoute('announce_show', ['id' => $announce]);

    }

    /**
     * @Route("/announce/confirm/{id}", name="announce_confirm", methods={"GET"})
     * @param Request $request
     * @param $id
     * @param TranslatorInterface $translator
     * @return mixed
     */
    public function confirm(Request $request, $id, TranslatorInterface $translator): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('confirm'.$id  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $announce = $em->getRepository(Announce::class)->find($id);
            $announce->setConfirm(self::CONFIRM);
            $em->flush();

            //TODO iş yeri onayında kullanıcıya bilgilendirme maili gönderilecek.

            $this->addFlash('success', $translator->trans('announce_confirmed'));

        }

        return $this->redirectToRoute('announce_show', ['id' => $id]);

    }

    /**
     * @Route("/announce/unconfirm/{id}", name="announce_unconfirm", methods={"GET"})
     * @param Request $request
     * @param $id
     * @param TranslatorInterface $translator
     * @return mixed
     */
    public function unconfirm(Request $request, $id, TranslatorInterface $translator): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('unconfirm'.$id  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $announce = $em->getRepository(Announce::class)->find($id);
            $announce->setConfirm(self::SAVE_AS_DRAFT);
            $em->flush();

            //todo yayından kaldırılma sebebi mail olarak gönderilebilir.

            $this->addFlash('success', $translator->trans('announce_unconfirmed'));

        }

        return $this->redirectToRoute('announce_show', ['id' => $id]);

    }

    /**
     * @Route("/save-as-draft/{id}", name="announce_save_as_draft", methods={"GET"})
     * @param Request $request
     * @param $id
     * @param TranslatorInterface $translator
     * @return mixed
     */
    public function saveAsDraft(Request $request, $id, TranslatorInterface $translator): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('announce_save_as_draft'.$id  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $advert = $em->getRepository(Announce::class)->find($id);
            $advert->setConfirm(self::SAVE_AS_DRAFT);
            $em->flush();

            $this->addFlash('success', $translator->trans('announce_confirm_cancelled'));
        }

        return $this->redirectToRoute('announce_show', ['id' => $id]);

    }

    /**
     * @Route("/send-confirmation-request/{id}", name="announce_send_confirmation_request", methods={"GET"})
     * @param Request $request
     * @param $id
     * @param TranslatorInterface $translator
     * @return mixed
     */
    public function sendConfirmationRequest(Request $request, $id, TranslatorInterface $translator): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('announce_send_confirmation_request'.$id  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $advert = $em->getRepository(Announce::class)->find($id);
            $advert->setConfirm(self::SEND_CONFIRMATION_REQUEST);
            $em->flush();

            $this->addFlash('success', $translator->trans('announce_send_confirmation_request'));
        }

        return $this->redirectToRoute('announce_show', ['id' => $id]);

    }

}
