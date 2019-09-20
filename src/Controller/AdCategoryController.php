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
    /**
     * @Route("/", name="ad_category_index", methods={"GET"})
     */
    public function index(AdCategoryRepository $adCategoryRepository): Response
    {
        return $this->render('ad_category/index.html.twig', [
            'ad_categories' => $adCategoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="ad_category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $adCategory = new AdCategory();
        $form = $this->createForm(AdCategoryType::class, $adCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($adCategory);
            $entityManager->flush();

            return $this->redirectToRoute('ad_category_index');
        }

        return $this->render('ad_category/new.html.twig', [
            'ad_category' => $adCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ad_category_show", methods={"GET"})
     */
    public function show(AdCategory $adCategory): Response
    {
        return $this->render('ad_category/show.html.twig', [
            'ad_category' => $adCategory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ad_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, AdCategory $adCategory): Response
    {
        $form = $this->createForm(AdCategoryType::class, $adCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ad_category_index');
        }

        return $this->render('ad_category/edit.html.twig', [
            'ad_category' => $adCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ad_category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, AdCategory $adCategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($adCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ad_category_index');
    }
}
