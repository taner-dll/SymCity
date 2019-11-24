<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
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
 * @Route("/event")
 */
class EventController extends AbstractController
{

    use File;
    use Util;


    const CONFIRM = 1;
    const SAVE_AS_DRAFT = 2;
    const SEND_CONFIRMATION_REQUEST = 0;

    /**
     * @Route("/", name="event_index", methods={"GET"})
     * @param EventRepository $eventRepository
     * @return Response
     */
    public function index(EventRepository $eventRepository): Response
    {

        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $events = $eventRepository->findAll();
        }
        else{
            $events = $eventRepository->findBy(array('user'=>$this->getUser()));
        }


        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * @Route("/new", name="event_new", methods={"GET","POST"})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            
            //url slug
            $event->setSlug($this->slugify($request->request->get('event')['name']));

            $event->setUser($this->getUser());
            $event->setLastUpdate(new DateTime('now'));
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('event_added'));

            //jquery-cropper (cropped image hidden input)
            $file = $request->request->get('cropped_image');

            //cropped image
            if(!empty($file)) {

                //dosya adı oluştur ve db güncelle
                $fileName = $this->base64generateFileName($file);
                $event->setImage($fileName);
                $entityManager->flush();

                //dosya yükle
                $dir = $this->getParameter('evt_directory');
                $this->base64upload($file, $dir, $fileName);
            }

            return $this->redirectToRoute('event_index');
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="event_show", methods={"GET"})
     * @param Event $event
     * @return Response
     */
    public function show(Event $event): Response
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
              if($event->getUser()->getId() != $this->getUser()->getId()){
                  return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
              }
        }

        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="event_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Event $event
     * @param TranslatorInterface $translator
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, Event $event, TranslatorInterface $translator): Response
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            if($event->getUser()->getId() != $this->getUser()->getId()){
                return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
            }
        }

        //eski resmi kaldırırken sorgu gerekti. product->getPicture() temp olarak gözüküyor?
        $em = $this->getDoctrine()->getManager();
        $p = $em->getRepository(Event::class)->find($event->getId());
        $fileOldName = $p->getImage();

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event->setImage($fileOldName);
            $event->setConfirm(0);//event guncellenince onay yeniden 0 olmalı.
            $event->setLastUpdate(new DateTime('now'));

            //url slug
            $event->setSlug($this->slugify($event->getName()));

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $translator->trans('event_updated'));

            //jquery-cropper (cropped image hidden input)
            $file = $request->request->get('cropped_image');

            //cropped image
            if(!empty($file)) {

                //dosya adı oluştur ve db güncelle
                $fileName = $this->base64generateFileName($file);
                $event->setImage($fileName);
                $em->flush();

                //dosya yükle
                $dir = $this->getParameter('evt_directory');
                $this->base64update($file, $dir, $fileName, $fileOldName);

            }

            return $this->redirectToRoute('event_show', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="event_delete", methods={"DELETE"})
     * @param Request $request
     * @param Event $event
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Request $request, Event $event, TranslatorInterface $translator): Response
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            if($event->getUser()->getId() != $this->getUser()->getId()){
                return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
            }
        }

        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {

            //öne çıkan resmi sil
            $dir = $this->getParameter('evt_directory');
            $this->deleteFile($dir, $event->getImage());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('event_deleted'));
        }

        return $this->redirectToRoute('event_index');
    }

    /**
     * @Route("/event/featured/photo/delete/{event}", name="event_featured_photo_delete", methods={"GET"})
     * @param Request $request
     * @param $event
     * @param TranslatorInterface $translator
     * @return mixed
     */
    public function deleteFeatured(Request $request,$event, TranslatorInterface $translator): Response
    {

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('delete-featured-photo'.$event  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $photo = $em->getRepository(Event::class)->find($event);

            if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                if($photo->getUser()->getId() != $this->getUser()->getId()){
                    return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
                }
            }

            $dir = $this->getParameter('evt_directory');
            $this->deleteFile($dir,$photo->getImage());

            $photo->setImage(null);
            $em->flush();

            $this->addFlash('success', $translator->trans('event_featured_image_deleted'));

        }

        return $this->redirectToRoute('event_show', ['id' => $event]);

    }

    /**
     * @Route("/event/confirm/{id}", name="event_confirm", methods={"GET"})
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
            $event = $em->getRepository(Event::class)->find($id);
            $event->setConfirm(self::CONFIRM);
            $em->flush();

            $this->addFlash('success', $translator->trans('event_confirmed'));

        }

        return $this->redirectToRoute('event_show', ['id' => $id]);

    }

    /**
     * @Route("/event/unconfirm/{id}", name="event_unconfirm", methods={"GET"})
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
            $event = $em->getRepository(Event::class)->find($id);
            $event->setConfirm(self::SAVE_AS_DRAFT);
            $em->flush();

            $this->addFlash('success', $translator->trans('event_unconfirmed'));

        }

        return $this->redirectToRoute('event_show', ['id' => $id]);

    }

    /**
     * @Route("/save-as-draft/{id}", name="event_save_as_draft", methods={"GET"})
     * @param Request $request
     * @param $id
     * @param TranslatorInterface $translator
     * @return mixed
     */
    public function saveAsDraft(Request $request, $id, TranslatorInterface $translator): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('event_save_as_draft'.$id  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $event = $em->getRepository(Event::class)->find($id);
            $event->setConfirm(self::SAVE_AS_DRAFT);
            $em->flush();

            $this->addFlash('success', $translator->trans('event_confirm_cancelled'));
        }

        return $this->redirectToRoute('event_show', ['id' => $id]);

    }


    /**
     * @Route("/send-confirmation-request/{id}", name="event_send_confirmation_request", methods={"GET"})
     * @param Request $request
     * @param $id
     * @param TranslatorInterface $translator
     * @return mixed
     */
    public function sendConfirmationRequest(Request $request, $id, TranslatorInterface $translator): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('event_send_confirmation_request'.$id  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $event = $em->getRepository(Event::class)->find($id);
            $event->setConfirm(self::SEND_CONFIRMATION_REQUEST);
            $em->flush();

            $this->addFlash('success', $translator->trans('event_send_confirmation_request'));
        }

        return $this->redirectToRoute('event_show', ['id' => $id]);

    }



}
