<?php

namespace App\Controller;

use App\Entity\Place;
use App\Entity\PlacesToVisit;
use App\Form\PlacesToVisitType;
use App\Repository\PlacesToVisitRepository;
use App\Traits\File;
use App\Traits\Util;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * @Route("/places/to/visit")
 */
class PlacesToVisitController extends AbstractController
{

    use File;
    use Util;

    /**
     * @Route("/", name="places_to_visit_index", methods={"GET"})
     * @param PlacesToVisitRepository $placesToVisitRepository
     * @return Response
     */
    public function index(PlacesToVisitRepository $placesToVisitRepository): Response
    {
        return $this->render('places_to_visit/index.html.twig', [
            'places_to_visits' => $placesToVisitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="places_to_visit_new", methods={"GET","POST"})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $placesToVisit = new PlacesToVisit();
        $form = $this->createForm(PlacesToVisitType::class, $placesToVisit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            //url slug
            $placesToVisit->setSlug($this->slugify($request->request->get('places_to_visit')['name']));

            if (isset($request->request->get('places_to_visit')['subPlace'])):
                $sub_place_id = $request->request->get('places_to_visit')['subPlace'];
                $placesToVisit->setSubPlace($entityManager->find(Place::class, $sub_place_id));
            endif;

            $entityManager->persist($placesToVisit);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('ptv_added'));

            //jquery-cropper (cropped image hidden input)
            $file = $request->request->get('cropped_image');

            //cropped image
            if (!empty($file)) {
                $em = $this->getDoctrine()->getManager();

                //dosya adı oluştur ve db güncelle
                $fileName = $this->base64generateFileName($file);
                $placesToVisit->setFeaturedPicture($fileName);
                $em->flush();

                //dosya yükle
                $dir = $this->getParameter('ptv_directory');
                $this->base64upload($file, $dir, $fileName);
            }

            return $this->redirectToRoute('places_to_visit_index');
        }
        return $this->render('places_to_visit/new.html.twig', [
            'places_to_visit' => $placesToVisit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="places_to_visit_show", methods={"GET"})
     * @param PlacesToVisit $placesToVisit
     * @return Response
     */
    public function show(PlacesToVisit $placesToVisit): Response
    {


        return $this->render('places_to_visit/show.html.twig', [
            'places_to_visit' => $placesToVisit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="places_to_visit_edit", methods={"GET","POST"})
     * @param Request $request
     * @param PlacesToVisit $placesToVisit
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, PlacesToVisit $placesToVisit, TranslatorInterface $translator): Response
    {
        //eski resmi kaldırırken sorgu gerekti. product->getPicture() temp olarak gözüküyor?
        $em = $this->getDoctrine()->getManager();
        $p = $em->getRepository(PlacesToVisit::class)->find($placesToVisit->getId());
        $fileOldName = $p->getFeaturedPicture();

        $form = $this->createForm(PlacesToVisitType::class, $placesToVisit);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            //yeni resim yoksa, boş olarak kaydedilmemeli!
            $placesToVisit->setFeaturedPicture($fileOldName);
            $placesToVisit->setSlug($this->slugify($placesToVisit->getName()));


            //dump($request->request->all());exit;

            if (isset($request->request->get('places_to_visit')['subPlace'])):
                $sub_place_id = $request->request->get('places_to_visit')['subPlace'];
                $placesToVisit->setSubPlace($em->find(Place::class, $sub_place_id));
            endif;


            $em->flush();
            $this->addFlash('success', $translator->trans('ptv_updated'));

            //jquery-cropper (cropped image hidden input)
            $file = $request->request->get('cropped_image');



            //cropped image
            if (!empty($file)) {

                //dosya adı oluştur ve db güncelle
                $fileName = $this->base64generateFileName($file);
                $placesToVisit->setFeaturedPicture($fileName);
                $em->flush();

                //dosya yükle
                $dir = $this->getParameter('ptv_directory');
                $this->base64update($file, $dir, $fileName, $fileOldName);
            }





            return $this->redirectToRoute('places_to_visit_show', [
                'id' => $placesToVisit->getId(),
            ]);
        }

        return $this->render('places_to_visit/edit.html.twig',  [
            'places_to_visit' => $placesToVisit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="places_to_visit_delete", methods={"DELETE"})
     * @param Request $request
     * @param PlacesToVisit $placesToVisit
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Request $request, PlacesToVisit $placesToVisit, TranslatorInterface $translator): Response
    {


        if ($this->isCsrfTokenValid('delete' . $placesToVisit->getId(), $request->request->get('_token'))) {

            //galeridekileri sil
            $gal_dir = $this->getParameter('gal_directory');
            foreach ($placesToVisit->getPTVPhotos() as $photo) {
                echo $photo->getFileName();
                $this->deleteFile($gal_dir, $photo->getFileName());
            }


            //öne çıkan resmi sil
            $dir = $this->getParameter('ptv_directory');
            $this->deleteFile($dir, $placesToVisit->getFeaturedPicture());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($placesToVisit);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('ptv_deleted'));
        }

        return $this->redirectToRoute('places_to_visit_index');
    }


    /**
     * @Route("/ptv/featured/photo/delete/{ptv}", name="ptv_featured_photo_delete", methods={"GET"})
     * @param Request $request
     * @param $ptv
     * @param TranslatorInterface $translator
     * @return mixed
     */
    public function deleteFeatured(Request $request, $ptv, TranslatorInterface $translator)
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('delete-featured-photo' . $ptv, $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $photo = $em->getRepository(PlacesToVisit::class)->find($ptv);

            $dir = $this->getParameter('ptv_directory');
            $this->deleteFile($dir, $photo->getFeaturedPicture());

            $photo->setFeaturedPicture(null);
            $em->flush();

            $this->addFlash('success', $translator->trans('ptv_featured_image_deleted'));
        }

        return $this->redirectToRoute('places_to_visit_show', ['id' => $ptv]);
    }
}
