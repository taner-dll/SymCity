<?php

namespace App\Controller;

use App\Entity\AdSubCategory;
use App\Form\AdSubCategory3Type;
use App\Repository\AdSubCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ad/sub/category")
 */
class AdSubCategoryController extends AbstractController
{
    /**
     * @Route("/", name="ad_sub_category_index", methods={"GET"})
     */
    public function index(AdSubCategoryRepository $adSubCategoryRepository): Response
    {
        return $this->render('ad_sub_category/index.html.twig', [
            'ad_sub_categories' => $adSubCategoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="ad_sub_category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $adSubCategory = new AdSubCategory();
        $form = $this->createForm(AdSubCategory3Type::class, $adSubCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($adSubCategory);
            $entityManager->flush();

            return $this->redirectToRoute('ad_sub_category_index');
        }

        return $this->render('ad_sub_category/new.html.twig', [
            'ad_sub_category' => $adSubCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ad_sub_category_show", methods={"GET"})
     */
    public function show(AdSubCategory $adSubCategory): Response
    {
        return $this->render('ad_sub_category/show.html.twig', [
            'ad_sub_category' => $adSubCategory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ad_sub_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, AdSubCategory $adSubCategory): Response
    {
        $form = $this->createForm(AdSubCategory3Type::class, $adSubCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ad_sub_category_index');
        }

        return $this->render('ad_sub_category/edit.html.twig', [
            'ad_sub_category' => $adSubCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ad_sub_category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, AdSubCategory $adSubCategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adSubCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($adSubCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ad_sub_category_index');
    }
}
