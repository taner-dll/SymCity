<?php

namespace App\Controller;

use App\Entity\AdSubCategory;
use App\Entity\Advert;
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

/**
 * @Route("/advert")
 */
class AdvertController extends AbstractController
{
    use File;
    use Util;
    /**
     * @Route("/", name="advert_index", methods={"GET"})
     * @param AdvertRepository $advertRepository
     * @return Response
     */
    public function index(AdvertRepository $advertRepository): Response
    {

        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $adverts = $advertRepository->findAll();
        }
        else{
            $adverts = $advertRepository->findBy(array('user'=>$this->getUser()));
        }

        return $this->render('advert/index.html.twig', [
            'adverts' => $adverts,
        ]);
    }

    /**
     * @Route("/new", name="advert_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {


        //return new JsonResponse($request->request->all());
        dump($request->request->all());exit;

        $advert = new Advert();
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {




            //url slug
            $advert->setSlug($this->slugify($request->request->get('advert')['title']));

            $advert->setUser($this->getUser());
            $advert->setLastUpdate(new DateTime('now'));

            //0: bekliyor, 1: onaylandı, 2: önizleme modu
            $advert->setStatus(2);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($advert);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully Added');

            //jquery-cropper (cropped image hidden input)
            $file = $request->request->get('cropped_image');

            //cropped image
            if(!empty($file)) {

                //dosya adı oluştur ve db güncelle
                $fileName = $this->base64generateFileName($file);
                $advert->setFeaturedImage($fileName);
                $entityManager->flush();

                //dosya yükle
                $dir = $this->getParameter('adv_directory');
                $this->base64upload($file, $dir, $fileName);
            }

            return $this->redirectToRoute('advert_index');
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
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            if($advert->getUser()->getId() != $this->getUser()->getId()){
                return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
            }
        }

        return $this->render('advert/show.html.twig', [
            'advert' => $advert,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="advert_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Advert $advert
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, Advert $advert): Response
    {

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            if($advert->getUser()->getId() != $this->getUser()->getId()){
                return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
            }
        }

        //eski resmi kaldırırken sorgu gerekti. product->getPicture() temp olarak gözüküyor?
        $em = $this->getDoctrine()->getManager();
        $p = $em->getRepository(Advert::class)->find($advert->getId());
        $fileOldName = $p->getFeaturedImage();

        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $advert->setFeaturedImage($fileOldName);
            $advert->setConfirm(0);//guncellenince onay yeniden 0 olmalı.
            $advert->setLastUpdate(new DateTime('now'));

            //url slug
            $advert->setSlug($this->slugify($advert->getTitle()));

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Successfully Updated');

            //jquery-cropper (cropped image hidden input)
            $file = $request->request->get('cropped_image');


            //cropped image
            if(!empty($file)) {

                //dosya adı oluştur ve db güncelle
                $fileName = $this->base64generateFileName($file);
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
     * @return Response
     */
    public function delete(Request $request, Advert $advert): Response
    {

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            if($advert->getUser()->getId() != $this->getUser()->getId()){
                return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
            }
        }

        if ($this->isCsrfTokenValid('delete'.$advert->getId(), $request->request->get('_token'))) {

            //öne çıkan resmi sil
            $dir = $this->getParameter('adv_directory');
            $this->deleteFile($dir, $advert->getFeaturedImage());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($advert);
            $entityManager->flush();
            $this->addFlash('success', 'Successfully Deleted');
        }

        return $this->redirectToRoute('advert_index');
    }


    /**
     * @Route("/advert/featured/photo/delete/{advert}", name="advert_featured_photo_delete", methods={"GET"})
     * @param Request $request
     * @param $advert
     * @return mixed
     */
    public function deleteFeatured(Request $request,$advert)
    {


        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('delete-featured-photo'.$advert  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $photo = $em->getRepository(Advert::class)->find($advert);

            if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                if($photo->getUser()->getId() != $this->getUser()->getId()){
                    return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
                }
            }

            $dir = $this->getParameter('adv_directory');
            $this->deleteFile($dir,$photo->getFeaturedImage());

            $photo->setFeaturedImage(null);
            $em->flush();

            $this->addFlash('success','Successfully Deleted');

        }

        return $this->redirectToRoute('advert_show', ['id' => $advert]);

    }

    /**
     * @Route("/advert/confirm/{id}", name="advert_confirm", methods={"GET"})
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function confirm(Request $request, $id)
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('confirm'.$id  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $advert = $em->getRepository(Advert::class)->find($id);
            $advert->setConfirm(1);
            $em->flush();

            $this->addFlash('success','Successfully Confirmed');

        }

        return $this->redirectToRoute('advert_show', ['id' => $id]);

    }

    /**
     * @Route("/advert/unconfirm/{id}", name="advert_unconfirm", methods={"GET"})
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function unconfirm(Request $request, $id)
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('unconfirm'.$id  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $advert = $em->getRepository(Advert::class)->find($id);
            $advert->setConfirm(0);
            $em->flush();

            $this->addFlash('success','Successfully Canceled');

        }

        return $this->redirectToRoute('advert_show', ['id' => $id]);

    }







}
