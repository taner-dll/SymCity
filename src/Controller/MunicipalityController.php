<?php

namespace App\Controller;

use App\Entity\Municipality;
use App\Form\MunicipalityType;
use App\Repository\MunicipalityRepository;
use App\Traits\FileTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/municipality")
 */
class MunicipalityController extends AbstractController
{
    use FileTrait;
    /**
     * @Route("/", name="municipality_index", methods={"GET"})
     * @param MunicipalityRepository $municipalityRepository
     * @return Response
     */
    public function index(MunicipalityRepository $municipalityRepository): Response
    {
        return $this->render('municipality/index.html.twig', [
            'municipalities' => $municipalityRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="municipality_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $municipality = new Municipality();
        $form = $this->createForm(MunicipalityType::class, $municipality);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            /*
            echo '<pre>';
            print_r($request->request->all());
            echo '</pre>';
            exit;
            */

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($municipality);
            $entityManager->flush();
            $this->addFlash('success', 'Successfully Added');

            //jquery-cropper (cropped image hidden input)
            $mayCroppedImage = $request->request->get('may_cropped_image');
            $munCroppedImage = $request->request->get('mun_cropped_image');

            if(!empty($mayCroppedImage)) {

                //dosya adı oluştur ve db güncelle
                $fileName = $this->base64generateFileName($mayCroppedImage);
                $municipality->setMayorPhoto($fileName);
                $entityManager->flush();

                //dosya yükle
                $dir = $this->getParameter('may_directory');
                $this->base64upload($mayCroppedImage, $dir, $fileName);
            }

            if(!empty($munCroppedImage)) {

                //dosya adı oluştur ve db güncelle
                $fileName = $this->base64generateFileName($munCroppedImage);
                $municipality->setFeaturedPicture($fileName);
                $entityManager->flush();

                //dosya yükle
                $dir = $this->getParameter('mun_directory');
                $this->base64upload($munCroppedImage, $dir, $fileName);
            }

            return $this->redirectToRoute('municipality_index');
        }

        return $this->render('municipality/new.html.twig', [
            'municipality' => $municipality,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="municipality_show", methods={"GET"})
     * @param Municipality $municipality
     * @return Response
     */
    public function show(Municipality $municipality): Response
    {
        return $this->render('municipality/show.html.twig', [
            'municipality' => $municipality,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="municipality_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Municipality $municipality
     * @return Response
     */
    public function edit(Request $request, Municipality $municipality): Response
    {

        //eski resmi kaldırırken sorgu gerekti. product->getPicture() temp olarak gözüküyor?
        $em = $this->getDoctrine()->getManager();
        $p = $em->getRepository(Municipality::class)->find($municipality->getId());
        $fileOldNameMun = $p->getFeaturedPicture();
        $fileOldNameMay = $p->getMayorPhoto();

        $form = $this->createForm(MunicipalityType::class, $municipality);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Successfully Updated');

            //jquery-cropper (cropped image hidden input)
            $mayCroppedImage = $request->request->get('may_cropped_image');
            $munCroppedImage = $request->request->get('mun_cropped_image');

            if(!empty($mayCroppedImage)) {

                //dosya adı oluştur ve db güncelle
                $fileName = $this->base64generateFileName($mayCroppedImage);
                $municipality->setMayorPhoto($fileName);
                $em->flush();

                //dosya yükle
                $dir = $this->getParameter('may_directory');
                $this->base64update($mayCroppedImage, $dir, $fileName, $fileOldNameMay);
            }

            if(!empty($munCroppedImage)) {

                //dosya adı oluştur ve db güncelle
                $fileName = $this->base64generateFileName($munCroppedImage);
                $municipality->setFeaturedPicture($fileName);
                $em->flush();

                //dosya yükle
                $dir = $this->getParameter('mun_directory');
                $this->base64update($munCroppedImage, $dir, $fileName, $fileOldNameMun);
            }

            return $this->redirectToRoute('municipality_index', [
                'id' => $municipality->getId(),
            ]);
        }

        return $this->render('municipality/edit.html.twig', [
            'municipality' => $municipality,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="municipality_delete", methods={"DELETE"})
     * @param Request $request
     * @param Municipality $municipality
     * @return Response
     */
    public function delete(Request $request, Municipality $municipality): Response
    {
        if ($this->isCsrfTokenValid('delete'.$municipality->getId(), $request->request->get('_token'))) {

            //resimleri sil
            $mun_dir = $this->getParameter('mun_directory');
            $may_dir = $this->getParameter('may_directory');
            $this->removeFeaturedPicture($mun_dir, $municipality->getFeaturedPicture());
            $this->removeFeaturedPicture($may_dir, $municipality->getMayorPhoto());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($municipality);
            $entityManager->flush();
            $this->addFlash('success', 'Successfully Deleted');
        }

        return $this->redirectToRoute('municipality_index');
    }
}
