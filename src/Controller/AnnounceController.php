<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Form\AnnounceType;
use App\Repository\AnnounceRepository;
use App\Traits\File;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/announce")
 */
class AnnounceController extends AbstractController
{

    use File;

    /**
     * @Route("/", name="announce_index", methods={"GET"})
     * @param AnnounceRepository $announceRepository
     * @return Response
     */
    public function index(AnnounceRepository $announceRepository): Response
    {
        return $this->render('announce/index.html.twig', [
            'announces' => $announceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="announce_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws /Exception
     */
    public function new(Request $request): Response
    {
        $announce = new Announce();
        $form = $this->createForm(AnnounceType::class, $announce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $announce->setUser($this->getUser());
            $announce->setLastUpdate(new DateTime('now'));
            $entityManager->persist($announce);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully Added');


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
        return $this->render('announce/show.html.twig', [
            'announce' => $announce,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="announce_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Announce $announce
     * @return Response
     * @throws /Exception
     */
    public function edit(Request $request, Announce $announce): Response
    {
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
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Successfully Updated');


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

            return $this->redirectToRoute('announce_index', [
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
     * @return Response
     */
    public function delete(Request $request, Announce $announce): Response
    {
        if ($this->isCsrfTokenValid('delete'.$announce->getId(), $request->request->get('_token'))) {

            //öne çıkan resmi sil
            $dir = $this->getParameter('ann_directory');
            $this->deleteFile($dir, $announce->getImage());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($announce);
            $entityManager->flush();
            $this->addFlash('success', 'Successfully Deleted');
        }

        return $this->redirectToRoute('announce_index');
    }

    /**
     * @Route("/announce/featured/photo/delete/{announce}", name="announce_featured_photo_delete", methods={"GET"})
     * @param Request $request
     * @param $announce
     * @return mixed
     */
    public function deleteFeatured(Request $request,$announce)
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('delete-featured-photo'.$announce  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $photo = $em->getRepository(Announce::class)->find($announce);

            $dir = $this->getParameter('ann_directory');
            $this->deleteFile($dir,$photo->getImage());

            $photo->setImage(null);
            $em->flush();

            $this->addFlash('success','Successfully Deleted');

        }

        return $this->redirectToRoute('announce_show', ['id' => $announce]);

    }
}
