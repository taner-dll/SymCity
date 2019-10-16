<?php

namespace App\Controller;

use App\Entity\PlacesToVisit;
use App\Entity\PTVPhoto;
use App\Traits\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PTVPhotoController extends AbstractController
{

    use File;


    /**
     * @Route("/ptv/photo/new", name="ptv_photo_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function new(Request $request): Response
    {
        //jquery-cropper (cropped image hidden input)
        $file = $request->request->get('cropped_image');
        $id = $request->request->get('id');
        $submittedToken = $request->request->get('token');


        //cropped image
        if (!empty($file)) {

            if ($this->isCsrfTokenValid('new-gallery-photo', $submittedToken)) {
                $fileName = $this->base64generateFileName($file);
                $dir = $this->getParameter('gal_directory');
                $this->base64upload($file, $dir, $fileName);

                $em = $this->getDoctrine()->getManager();
                $ptv = $em->getRepository(PlacesToVisit::class)->find($id);

                $gallery = new PTVPhoto();
                $gallery->setPtv($ptv);
                $gallery->setDateAdded(new \DateTime('now'));
                $gallery->setFileName($fileName);
                $em->persist($gallery);
                $em->flush();

                $this->addFlash('success', 'Successfully Added');
            }

        }

        return $this->redirectToRoute('places_to_visit_show', ['id' => $id]);

    }

    /**
     * @Route("/ptv/photo/delete/{ptv}/{id}", name="ptv_photo_delete", methods={"GET"})
     * @param Request $request
     * @param $ptv
     * @param $id
     * @return mixed
     */
    public function delete(Request $request,$ptv,$id)
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('delete-gallery-photo'.$id  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $photo = $em->getRepository(PTVPhoto::class)->find($id);

            $dir = $this->getParameter('gal_directory');
            $this->deleteFile($dir,$photo->getFileName());

            $em->remove($photo);
            $em->flush();

            $this->addFlash('success','Successfully Deleted');

        }

        return $this->redirectToRoute('places_to_visit_show', ['id' => $ptv]);

    }
}
