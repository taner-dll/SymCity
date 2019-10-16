<?php

namespace App\Controller;

use App\Entity\AdCategory;
use App\Entity\AdSubCategory;
use App\Form\AdSubCategoryType;
use App\Repository\AdSubCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param AdSubCategoryRepository $adSubCategoryRepository
     * @return Response
     */
    public function index(AdSubCategoryRepository $adSubCategoryRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');


        /*
        echo '<pre>';
        print_r($this->getDoctrine()->getRepository(AdCategory::class)->subCategorySort());
        echo '</pre>';
        */


        /*
        $response = new Response(json_encode($this->getDoctrine()->getRepository(AdCategory::class)->subCategorySort())
            ,Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
        */


        /*
        foreach ($this->getDoctrine()->getRepository(AdCategory::class)->subCategorySort() as $cat){
             echo $cat['cat_id'].'-'.$cat['cat_sn'].'-'.$cat['sub_sn'].'-'.$cat['sub_sort'].'<br>';
        }
        exit;
        */
        

        return $this->render('ad_sub_category/index.html.twig', [

            'category_names' => $this->getDoctrine()->getRepository(AdCategory::class)->subCategorySort()
        ]);
    }

    /**
     * @Route("/new", name="ad_sub_category_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $adSubCategory = new AdSubCategory();
        $form = $this->createForm(AdSubCategoryType::class, $adSubCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($adSubCategory);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully Added');

            return $this->redirectToRoute('ad_sub_category_index');
        }

        return $this->render('ad_sub_category/new.html.twig', [
            'ad_sub_category' => $adSubCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ad_sub_category_show", methods={"GET"})
     * @param AdSubCategory $adSubCategory
     * @return Response
     */
    public function show(AdSubCategory $adSubCategory): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('ad_sub_category/show.html.twig', [
            'ad_sub_category' => $adSubCategory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ad_sub_category_edit", methods={"GET","POST"})
     * @param Request $request
     * @param AdSubCategory $adSubCategory
     * @return Response
     */
    public function edit(Request $request, AdSubCategory $adSubCategory): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(AdSubCategoryType::class, $adSubCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Successfully Updated');

            return $this->redirectToRoute('ad_sub_category_index');
        }

        return $this->render('ad_sub_category/edit.html.twig', [
            'ad_sub_category' => $adSubCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ad_sub_category_delete", methods={"DELETE"})
     * @param Request $request
     * @param AdSubCategory $adSubCategory
     * @return Response
     */
    public function delete(Request $request, AdSubCategory $adSubCategory): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $adSubCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($adSubCategory);
            $entityManager->flush();
            $this->addFlash('success', 'Successfully Deleted');
        }

        return $this->redirectToRoute('ad_sub_category_index');
    }
}
