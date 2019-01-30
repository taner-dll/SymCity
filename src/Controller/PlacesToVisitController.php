<?php

namespace App\Controller;

use App\Entity\PlacesToVisit;
use App\Form\PlacesToVisitType;
use App\Repository\PlacesToVisitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/places/to/visit")
 */
class PlacesToVisitController extends AbstractController
{
    /**
     * @Route("/", name="places_to_visit_index", methods={"GET"})
     * @param PlacesToVisitRepository $placesToVisitRepository
     * @return Response
     */
    public function index(PlacesToVisitRepository $placesToVisitRepository): Response
    {
        return $this->render('places_to_visit/index.html.twig', [
            'places_to_visits' => $placesToVisitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="places_to_visit_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $placesToVisit = new PlacesToVisit();
        $form = $this->createForm(PlacesToVisitType::class, $placesToVisit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($placesToVisit);
            $entityManager->flush();

            return $this->redirectToRoute('places_to_visit_index');
        }

        return $this->render('places_to_visit/new.html.twig', [
            'places_to_visit' => $placesToVisit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="places_to_visit_show", methods={"GET"})
     * @param PlacesToVisit $placesToVisit
     * @return Response
     */
    public function show(PlacesToVisit $placesToVisit): Response
    {
        return $this->render('places_to_visit/show.html.twig', [
            'places_to_visit' => $placesToVisit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="places_to_visit_edit", methods={"GET","POST"})
     * @param Request $request
     * @param PlacesToVisit $placesToVisit
     * @return Response
     */
    public function edit(Request $request, PlacesToVisit $placesToVisit): Response
    {
        $form = $this->createForm(PlacesToVisitType::class, $placesToVisit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('places_to_visit_index', [
                'id' => $placesToVisit->getId(),
            ]);
        }

        return $this->render('places_to_visit/edit.html.twig', [
            'places_to_visit' => $placesToVisit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="places_to_visit_delete", methods={"DELETE"})
     * @param Request $request
     * @param PlacesToVisit $placesToVisit
     * @return Response
     */
    public function delete(Request $request, PlacesToVisit $placesToVisit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$placesToVisit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($placesToVisit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('places_to_visit_index');
    }
}
