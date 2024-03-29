<?php

namespace App\Controller;

use App\Entity\PTVCategory;
use App\Form\PTVCategoryType;
use App\Repository\PTVCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/p/t/v/category")
 */
class PTVCategoryController extends AbstractController
{
    /**
     * @Route("/", name="p_t_v_category_index", methods={"GET"})
     * @param PTVCategoryRepository $pTVCategoryRepository
     * @return Response
     */
    public function index(PTVCategoryRepository $pTVCategoryRepository): Response
    {
        return $this->render('ptv_category/index.html.twig', [
            'p_t_v_categories' => $pTVCategoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="p_t_v_category_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $pTVCategory = new PTVCategory();
        $form = $this->createForm(PTVCategoryType::class, $pTVCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pTVCategory);
            $entityManager->flush();

            $this->addFlash('success', 'Gezilecek yer kategorisi başarıyla eklendi.');

            return $this->redirectToRoute('p_t_v_category_index');
        }

        return $this->render('ptv_category/new.html.twig', [
            'p_t_v_category' => $pTVCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="p_t_v_category_show", methods={"GET"})
     * @param PTVCategory $pTVCategory
     * @return Response
     */
    public function show(PTVCategory $pTVCategory): Response
    {
        return $this->render('ptv_category/show.html.twig', [
            'p_t_v_category' => $pTVCategory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="p_t_v_category_edit", methods={"GET","POST"})
     * @param Request $request
     * @param PTVCategory $pTVCategory
     * @return Response
     */
    public function edit(Request $request, PTVCategory $pTVCategory): Response
    {
        $form = $this->createForm(PTVCategoryType::class, $pTVCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Gezilecek yer kategorisi başarıyla güncellendi.');

            return $this->redirectToRoute('p_t_v_category_index');
        }

        return $this->render('ptv_category/edit.html.twig', [
            'p_t_v_category' => $pTVCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="p_t_v_category_delete", methods={"DELETE"})
     * @param Request $request
     * @param PTVCategory $pTVCategory
     * @return Response
     */
    public function delete(Request $request, PTVCategory $pTVCategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pTVCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($pTVCategory);
            $entityManager->flush();
            $this->addFlash('success', 'Gezilecek yer kategorisi başarıyla silindi.');
        }

        return $this->redirectToRoute('p_t_v_category_index');
    }
}
