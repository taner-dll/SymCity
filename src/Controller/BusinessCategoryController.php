<?php

namespace App\Controller;

use App\Entity\BusinessCategory;
use App\Form\BusinessCategoryType;
use App\Repository\BusinessCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/business/category")
 */
class BusinessCategoryController extends AbstractController
{
    /**
     * @Route("/", name="business_category_index", methods={"GET"})
     * @param BusinessCategoryRepository $businessCategoryRepository
     * @return Response
     */
    public function index(BusinessCategoryRepository $businessCategoryRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('business_category/index.html.twig', [
            'business_categories' => $businessCategoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="business_category_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $businessCategory = new BusinessCategory();
        $form = $this->createForm(BusinessCategoryType::class, $businessCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($businessCategory);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully Added');

            return $this->redirectToRoute('business_category_index');
        }

        return $this->render('business_category/new.html.twig', [
            'business_category' => $businessCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="business_category_show", methods={"GET"})
     * @param BusinessCategory $businessCategory
     * @return Response
     */
    public function show(BusinessCategory $businessCategory): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('business_category/show.html.twig', [
            'business_category' => $businessCategory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="business_category_edit", methods={"GET","POST"})
     * @param Request $request
     * @param BusinessCategory $businessCategory
     * @return Response
     */
    public function edit(Request $request, BusinessCategory $businessCategory): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(BusinessCategoryType::class, $businessCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Successfully Updated');

            return $this->redirectToRoute('business_category_index');
        }

        return $this->render('business_category/edit.html.twig', [
            'business_category' => $businessCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="business_category_delete", methods={"DELETE"})
     * @param Request $request
     * @param BusinessCategory $businessCategory
     * @return Response
     */
    public function delete(Request $request, BusinessCategory $businessCategory): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$businessCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($businessCategory);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully Deleted');
        }

        return $this->redirectToRoute('business_category_index');
    }
}
