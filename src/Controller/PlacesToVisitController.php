<?php

namespace App\Controller;

use App\Entity\PlacesToVisit;
use App\Form\PlacesToVisitType;
use App\Repository\PlacesToVisitRepository;
use App\Traits\FileTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/places/to/visit")
 */
class PlacesToVisitController extends AbstractController
{

    use FileTrait;

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
     * @return Response
     */
    public function new(Request $request): Response
    {
        $placesToVisit = new PlacesToVisit();
        $form = $this->createForm(PlacesToVisitType::class, $placesToVisit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($placesToVisit);
            $entityManager->flush();
            $this->addFlash('success', 'Successfully Added');

            //jquery-cropper (cropped image hidden input)
            $file = $request->request->get('cropped_image');

            //cropped image
            if(!empty($file)) {
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
     * @return Response
     */
    public function edit(Request $request, PlacesToVisit $placesToVisit): Response
    {
        //eski resmi kaldırırken sorgu gerekti. product->getPicture() temp olarak gözüküyor?
        $em = $this->getDoctrine()->getManager();
        $p = $em->getRepository(PlacesToVisit::class)->find($placesToVisit->getId());
        $fileOldName = $p->getFeaturedPicture();

        $form = $this->createForm(PlacesToVisitType::class, $placesToVisit);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Successfully Updated');

            //jquery-cropper (cropped image hidden input)
            $file = $request->request->get('cropped_image');


            //cropped image
            if(!empty($file)) {

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
     * @return Response
     */
    public function delete(Request $request, PlacesToVisit $placesToVisit): Response
    {
        if ($this->isCsrfTokenValid('delete' . $placesToVisit->getId(), $request->request->get('_token'))) {

            $dir = $this->getParameter('ptv_directory');
            $this->removeFeaturedPicture($dir, $placesToVisit->getFeaturedPicture());


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($placesToVisit);
            $entityManager->flush();
            $this->addFlash('success', 'Successfully Deleted');
        }

        return $this->redirectToRoute('places_to_visit_index');
    }
}