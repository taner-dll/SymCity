<?php

namespace App\Controller;

use App\Entity\Business;
use App\Entity\Event;
use App\Form\BusinessType;
use App\Repository\BusinessRepository;
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
 * @Route("/business")
 */
class BusinessController extends AbstractController
{

    use File;
    use Util;


    /**
     * @Route("/", name="business_index", methods={"GET"})
     * @param BusinessRepository $businessRepository
     * @return Response
     */
    public function index(BusinessRepository $businessRepository): Response
    {

        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $business = $businessRepository->findAll();
        }
        else{
            $business = $businessRepository->findBy(array('user'=>$this->getUser()));
        }

        return $this->render('business/index.html.twig', [
            'businesses' => $business,
        ]);
    }

    /**
     * @Route("/new", name="business_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $business = new Business();
        $form = $this->createForm(BusinessType::class, $business);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            //url slug
            $business->setSlug($this->slugify($request->request->get('business')['name']));

            $business->setUser($this->getUser());
            $business->setLastUpdate(new DateTime('now'));
            $entityManager->persist($business);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully Added');

            //jquery-cropper (cropped image hidden input)
            $file = $request->request->get('cropped_image');


            //cropped image
            if(!empty($file)) {

                //dosya adı oluştur ve db güncelle
                $fileName = $this->base64generateFileName($file);
                $business->setFeaturedPicture($fileName);
                $entityManager->flush();

                //dosya yükle
                $dir = $this->getParameter('bus_directory');
                $this->base64upload($file, $dir, $fileName);
            }



            return $this->redirectToRoute('business_index');
        }

        return $this->render('business/new.html.twig', [
            'business' => $business,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="business_show", methods={"GET"})
     * @param Business $business
     * @return Response
     */
    public function show(Business $business): Response
    {

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            if($business->getUser()->getId() != $this->getUser()->getId()){
                return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
            }
        }

        return $this->render('business/show.html.twig', [
            'business' => $business,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="business_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Business $business
     * @return Response
     */
    public function edit(Request $request, Business $business): Response
    {

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            if($business->getUser()->getId() != $this->getUser()->getId()){
                return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
            }
        }


        //eski resmi kaldırırken sorgu gerekti. product->getPicture() temp olarak gözüküyor?
        $em = $this->getDoctrine()->getManager();
        $p = $em->getRepository(Business::class)->find($business->getId());
        $fileOldName = $p->getFeaturedPicture();

        $form = $this->createForm(BusinessType::class, $business);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $business->setFeaturedPicture($fileOldName);
            $business->setConfirm(0);//event guncellenince onay yeniden 0 olmalı.
            $business->setLastUpdate(new DateTime('now'));

            //url slug
            $business->setSlug($this->slugify($business->getName()));

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Successfully Updated');

            //jquery-cropper (cropped image hidden input)
            $file = $request->request->get('cropped_image');

            //cropped image
            if(!empty($file)) {

                //dosya adı oluştur ve db güncelle
                $fileName = $this->base64generateFileName($file);
                $business->setFeaturedPicture($fileName);
                $em->flush();

                //dosya yükle
                $dir = $this->getParameter('bus_directory');
                $this->base64update($file, $dir, $fileName, $fileOldName);

            }

            return $this->redirectToRoute('business_index', [
                'id' => $business->getId(),
            ]);
        }

        return $this->render('business/edit.html.twig', [
            'business' => $business,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="business_delete", methods={"DELETE"})
     * @param Request $request
     * @param Business $business
     * @return Response
     */
    public function delete(Request $request, Business $business): Response
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            if($business->getUser()->getId() != $this->getUser()->getId()){
                return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
            }
        }

        if ($this->isCsrfTokenValid('delete'.$business->getId(), $request->request->get('_token'))) {

            //öne çıkan resmi sil
            $dir = $this->getParameter('bus_directory');
            $this->deleteFile($dir, $business->getFeaturedPicture());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($business);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully Deleted');
        }

        return $this->redirectToRoute('business_index');
    }


    /**
     * @Route("/business/confirm/{id}", name="business_confirm", methods={"GET"})
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
            $business = $em->getRepository(Business::class)->find($id);
            $business->setConfirm(1);
            $em->flush();

            $this->addFlash('success','Successfully Confirmed');

        }

        return $this->redirectToRoute('business_show', ['id' => $id]);

    }

    /**
     * @Route("/business/unconfirm/{id}", name="business_unconfirm", methods={"GET"})
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
            $business = $em->getRepository(Business::class)->find($id);
            $business->setConfirm(0);
            $em->flush();

            $this->addFlash('success','Successfully Canceled');
        }

        return $this->redirectToRoute('business_show', ['id' => $id]);

    }

    /**
     * @Route("/business/featured/photo/delete/{business}", name="business_featured_photo_delete", methods={"GET"})
     * @param Request $request
     * @param $business
     * @return mixed
     */
    public function deleteFeatured(Request $request,$business)
    {

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('delete-featured-photo'.$business  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $photo = $em->getRepository(Business::class)->find($business);

            if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                if($photo->getUser()->getId() != $this->getUser()->getId()){
                    return new JsonResponse('Bad Request.', Response::HTTP_FORBIDDEN);
                }
            }

            $dir = $this->getParameter('bus_directory');
            $this->deleteFile($dir,$photo->getFeaturedPicture());

            $photo->setFeaturedPicture(null);
            $em->flush();

            $this->addFlash('success','Successfully Deleted');

        }

        return $this->redirectToRoute('business_show', ['id' => $business]);

    }
}
