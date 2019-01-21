<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductCollection;
use App\Form\ProductCollectionType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product/collection")
 */
class ProductCollectionController extends AbstractController
{

    /**
     * @Route("/", name="product_collection_index", methods="GET")
     */
    public function index(): Response
    {
        $productCollections = $this->getDoctrine()
            ->getRepository(ProductCollection::class)
            ->findAll();

        return $this->render('product_collection/index.html.twig', ['product_collections' => $productCollections]);
    }

    /**
     * @Route("/new", name="product_collection_new", methods="GET|POST")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $productCollection = new ProductCollection();
        $form = $this->createForm(ProductCollectionType::class, $productCollection);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            //yeni resim varsa
            if($form->get('picture')->getData()){
                $file = $form->get('picture')->getData();
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('collections_directory'),
                        $fileName
                    );
                    $productCollection->setPicture($fileName);

                } catch (FileException $e) {
                    return new JsonResponse($form->get('picture')->getData());
                }
            }



            $em = $this->getDoctrine()->getManager();
            $em->persist($productCollection);
            $em->flush();

            $this->addFlash('success', 'Koleksiyon ekleme işlemi başarıyla gerçekleşti');

            return $this->redirectToRoute('product_collection_index');
        }

        return $this->render('product_collection/new.html.twig', [
            'product_collection' => $productCollection,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_collection_show", methods="GET")
     * @param ProductCollection $productCollection
     * @return Response
     */
    public function show(ProductCollection $productCollection): Response
    {

        return $this->render('product_collection/show.html.twig', ['product_collection' => $productCollection]);
    }

    /**
     * @Route("/{id}/edit", name="product_collection_edit", methods="GET|POST")
     * @param Request $request
     * @param ProductCollection $productCollection
     * @return Response
     */
    public function edit(Request $request, ProductCollection $productCollection): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(ProductCollectionType::class, $productCollection);
        $form->handleRequest($request);

        //sadece resim kaldır isteği varsa.
        if($request->query->get('resim_kaldir')==1){

            $fs = new Filesystem();
            $file = $this->getParameter('collections_directory').'/'.$productCollection->getPicture();

            if($fs->exists($file)){
                $fs->remove($file);
            }

            $productCollection->setPicture(null);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success','Resim başarıyla kaldırıldı');
            return $this->redirectToRoute('product_collection_show', ['id' => $productCollection->getId()]);
        }

        if ($form->isSubmitted() && $form->isValid()) {


            //yeni resim varsa
            if($form->get('picture')->getData()){

                $file = $form->get('picture')->getData();
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('collections_directory'),
                        $fileName
                    );
                    $productCollection->setPicture($fileName);


                } catch (FileException $e) {
                    return new JsonResponse($form->get('picture')->getData());
                }
            }
            else{
                //yeni resim yoksa, eskisiyle güncelle.
                $productCollection->setPicture($request->request->get('file_name'));
            }


            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success','Koleksiyon başarıyla güncellendi');

            return $this->redirectToRoute('product_collection_show', ['id' => $productCollection->getId()]);
        }

        return $this->render('product_collection/edit.html.twig', [
            'product_collection' => $productCollection,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_collection_delete", methods="DELETE")
     * @param Request $request
     * @param ProductCollection $productCollection
     * @return Response
     */
    public function delete(Request $request, ProductCollection $productCollection): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');


        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository(Product::class)->findBy(array('collection' => $productCollection->getId()));

        if (count($products) > 0) {
            $this->addFlash('notice', 'Bu koleksiyona tanımlı ürünler olduğundan silinemez');
            return $this->redirectToRoute('product_collection_show', array('id' => $productCollection->getId()));
        }

        if ($this->isCsrfTokenValid('delete' . $productCollection->getId(), $request->request->get('_token'))) {



            if($productCollection->getPicture() && $productCollection->getPicture()!=""){
                if(strlen($productCollection->getPicture())>0){

                    $fs = new Filesystem();
                    $file = $this->getParameter('collections_directory').'/'.$productCollection->getPicture();

                    if($fs->exists($file)){
                        $fs->remove($file);
                    }

                }
            }


            $em->remove($productCollection);
            $em->flush();
            $this->addFlash('success', 'Koleksiyon silme işlemi başarıyla gerçekleşti');



        }

        return $this->redirectToRoute('product_collection_index');


    }
}
