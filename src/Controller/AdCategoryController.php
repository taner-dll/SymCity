<?php

namespace App\Controller;

use App\Entity\AdCategory;
use App\Form\AdCategoryType;
use App\Repository\AdCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ad/category")
 */
class AdCategoryController extends AbstractController
{


    //TODO job, real-estate, vehicle... silinemez olmalı.

    /**
     * @Route("/", name="ad_category_index", methods={"GET"})
     * @param AdCategoryRepository $adCategoryRepository
     * @return Response
     */
    public function index(AdCategoryRepository $adCategoryRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('ad_category/index.html.twig', [
            'ad_categories' => $adCategoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="ad_category_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $adCategory = new AdCategory();
        $form = $this->createForm(AdCategoryType::class, $adCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($adCategory);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully Added');

            return $this->redirectToRoute('ad_category_index');
        }

        return $this->render('ad_category/new.html.twig', [
            'ad_category' => $adCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ad_category_show", methods={"GET"})
     * @param AdCategory $adCategory
     * @return Response
     */
    public function show(AdCategory $adCategory): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('ad_category/show.html.twig', [
            'ad_category' => $adCategory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ad_category_edit", methods={"GET","POST"})
     * @param Request $request
     * @param AdCategory $adCategory
     * @return Response
     */
    public function edit(Request $request, AdCategory $adCategory): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(AdCategoryType::class, $adCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Successfully Updated');

            return $this->redirectToRoute('ad_category_index');
        }

        return $this->render('ad_category/edit.html.twig', [
            'ad_category' => $adCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ad_category_delete", methods={"DELETE"})
     * @param Request $request
     * @param AdCategory $adCategory
     * @return Response
     */
    public function delete(Request $request, AdCategory $adCategory): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$adCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($adCategory);
            $entityManager->flush();
            $this->addFlash('success', 'Successfully Deleted');
        }

        return $this->redirectToRoute('ad_category_index');
    }
}
