<?php

namespace App\Controller;

use App\Entity\CollectionGroup;
use App\Entity\ProductCollection;
use App\Form\CollectionGroupType;
use App\Repository\CollectionGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/collection/group")
 */
class CollectionGroupController extends AbstractController
{
    /**
     * @Route("/", name="collection_group_index", methods={"GET"})
     * @param CollectionGroupRepository $collectionGroupRepository
     * @return Response
     */
    public function index(CollectionGroupRepository $collectionGroupRepository): Response
    {
        return $this->render('collection_group/index.html.twig', ['collection_groups' => $collectionGroupRepository->findAll()]);
    }

    /**
     * @Route("/new", name="collection_group_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $collectionGroup = new CollectionGroup();
        $form = $this->createForm(CollectionGroupType::class, $collectionGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $collectionGroup->setEnable(1);
            $entityManager->persist($collectionGroup);
            $entityManager->flush();

            return $this->redirectToRoute('collection_group_index');
        }

        return $this->render('collection_group/new.html.twig', [
            'collection_group' => $collectionGroup,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/update-collection-status", name="update_collection_status")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateCollectionStatus(Request $request){

        $collection_id = $request->request->get('collection_id');
        $status = $request->request->get('status');


        $em = $this->getDoctrine()->getManager();

        $collection = $this->getDoctrine()->getRepository(CollectionGroup::class)
            ->find($collection_id);

        $collection->setEnable($status);
        $em->flush();
        $em->clear();

        return $this->redirectToRoute('collection_group_show', array('id'=> $collection_id));


    }


    /**
     * @Route("/{id}", name="collection_group_show", methods={"GET"})
     * @param CollectionGroup $collectionGroup
     * @return Response
     */
    public function show(CollectionGroup $collectionGroup): Response
    {
        return $this->render('collection_group/show.html.twig', ['collection_group' => $collectionGroup]);
    }

    /**
     * @Route("/{id}/edit", name="collection_group_edit", methods={"GET","POST"})
     * @param Request $request
     * @param CollectionGroup $collectionGroup
     * @return Response
     */
    public function edit(Request $request, CollectionGroup $collectionGroup): Response
    {
        $form = $this->createForm(CollectionGroupType::class, $collectionGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Koleksiyon grubu güncelleme işlemi başarıyla gerçekleşti');
            return $this->redirectToRoute('collection_group_index', ['id' => $collectionGroup->getId()]);
        }

        return $this->render('collection_group/edit.html.twig', [
            'collection_group' => $collectionGroup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="collection_group_delete", methods={"DELETE"})
     * @param Request $request
     * @param CollectionGroup $collectionGroup
     * @return Response
     */
    public function delete(Request $request, CollectionGroup $collectionGroup): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $em = $this->getDoctrine()->getManager();
        $altGrup = $em->getRepository(ProductCollection::class)->findBy(array('collectionGroup' => $collectionGroup->getId()));


        if (count($altGrup) > 0) {
            $this->addFlash('notice', 'Bu koleksiyona tanımlı alt gruplar olduğundan silinemez');
            return $this->redirectToRoute('collection_group_show', array('id' => $collectionGroup->getId()));
        }

        if ($this->isCsrfTokenValid('delete'.$collectionGroup->getId(), $request->request->get('_token'))) {

            $em->remove($collectionGroup);
            $em->flush();
            $this->addFlash('success', 'Koleksiyon grubu silme işlemi başarıyla gerçekleşti');
        }

        return $this->redirectToRoute('collection_group_index');
    }
}
