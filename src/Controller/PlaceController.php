<?php

namespace App\Controller;


use App\Entity\Place;
use App\Form\PlaceType;
use App\Repository\PlaceRepository;
use App\Traits\FileTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/place")
 */
class PlaceController extends AbstractController
{

    use FileTrait;
    /**
     * @Route("/", name="place_index", methods={"GET"})
     * @param PlaceRepository $placeRepository
     * @return Response
     */
    public function index(PlaceRepository $placeRepository): Response
    {
        return $this->render('place/index.html.twig', [
            'places' => $placeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="place_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $place = new Place();
        $form = $this->createForm(PlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($place);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully Added');

            //jquery-cropper (cropped image hidden input)
            $file = $request->request->get('cropped_image');

            //cropped image
            if(!empty($file)) {

                //dosya adı oluştur ve db güncelle
                $fileName = $this->base64generateFileName($file);
                $place->setFeaturedImage($fileName);
                $entityManager->flush();

                //dosya yükle
                $dir = $this->getParameter('plc_directory');
                $this->base64upload($file, $dir, $fileName);
            }

            return $this->redirectToRoute('place_index');
        }

        return $this->render('place/new.html.twig', [
            'place' => $place,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="place_show", methods={"GET"})
     * @param Place $place
     * @return Response
     */
    public function show(Place $place): Response
    {
        return $this->render('place/show.html.twig', [
            'place' => $place,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="place_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Place $place
     * @return Response
     */
    public function edit(Request $request, Place $place): Response
    {
        //eski resmi kaldırırken sorgu gerekti. product->getPicture() temp olarak gözüküyor?
        $em = $this->getDoctrine()->getManager();
        $p = $em->getRepository(Place::class)->find($place->getId());
        $fileOldName = $p->getFeaturedImage();

        $form = $this->createForm(PlaceType::class, $place);
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
                $place->setFeaturedImage($fileName);
                $em->flush();

                //dosya yükle
                $dir = $this->getParameter('plc_directory');
                $this->base64update($file, $dir, $fileName, $fileOldName);

            }

            return $this->redirectToRoute('place_index', [
                'id' => $place->getId(),
            ]);
        }

        return $this->render('place/edit.html.twig', [
            'place' => $place,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="place_delete", methods={"DELETE"})
     * @param Request $request
     * @param Place $place
     * @return Response
     */
    public function delete(Request $request, Place $place): Response
    {
        if ($this->isCsrfTokenValid('delete'.$place->getId(), $request->request->get('_token'))) {

            //öne çıkan resmi sil
            $dir = $this->getParameter('plc_directory');
            $this->deleteFile($dir, $place->getFeaturedImage());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($place);
            $entityManager->flush();
            $this->addFlash('success', 'Successfully Deleted');
        }

        return $this->redirectToRoute('place_index');
    }

    /**
     * @Route("/place/featured/photo/delete/{place}", name="place_featured_photo_delete", methods={"GET"})
     * @param Request $request
     * @param $place
     * @return mixed
     */
    public function deleteFeatured(Request $request,$place)
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('delete-featured-photo'.$place  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $photo = $em->getRepository(Place::class)->find($place);

            $dir = $this->getParameter('plc_directory');
            $this->deleteFile($dir,$photo->getFeaturedImage());

            $photo->setFeaturedImage(null);
            $em->flush();

            $this->addFlash('success','Successfully Deleted');

        }

        return $this->redirectToRoute('place_show', ['id' => $place]);

    }
}
