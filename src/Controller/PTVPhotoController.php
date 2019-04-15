<?php

namespace App\Controller;

use App\Traits\FileTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PTVPhotoController extends AbstractController
{

    use FileTrait;

    /**
     * @Route("/ptv/photo/new", name="ptv_photo_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        print_r($_FILES);exit;

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
    }
}
