<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{


    /**
     * @Route("/", name="product_index", methods="GET")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {

        $repo = $this->getDoctrine()->getRepository(Product::class);


        if ($rp = $request->query->get('related-products')) {
            $product = $repo->relatedProducts($rp);
        } else {
            $product = $repo->findAll();
        }


        /*
        $pagination = $paginator->paginate(
            $product,
            $request->query->getInt('page',1),
            6
        );
        */
        return $this->render('product/index.html.twig', ['products' => $product]);
    }

    /**
     * @Route("/new", name="product_new", methods="GET|POST")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            //resim varsa
            if ($form->get('picture')->getData()) {
                $file = $form->get('picture')->getData();
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('products_directory'),
                        $fileName
                    );
                    $product->setPicture($fileName);


                } catch (FileException $e) {
                    return new JsonResponse($form->get('picture')->getData());
                }
            }

            //özellik ilişkisiz ise boş olarak ekle
            if ($form->get('property')->getData() === 'custom') {
                $product->setCollection(null);
            }


            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Ürün ekleme işlemi başarıyla gerçekleşti');

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods="GET")
     * @param Product $product
     * @return Response
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', ['product' => $product]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods="GET|POST")
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function edit(Request $request, Product $product): Response
    {




        // eski resmi kaldırırken sorgu gerekti.
        // $product->getPicture() temp olarak gözüküyor?
        $em = $this->getDoctrine()->getManager();
        $p = $em->getRepository(Product::class)->find($product->getId());
        $picOldName = $p->getPicture();


        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);


        //sadece resim kaldır isteği varsa.
        if ($request->query->get('resim_kaldir') == 1) {

            //kontrollü kaldır.
            if ($picOldName && $picOldName != "") {
                if (strlen($picOldName) > 0) {
                    $fs = new Filesystem();
                    $file = $this->getParameter('products_directory') . '/' . $picOldName;
                    $ext = explode('.', $picOldName);
                    $exts = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
                    if (in_array($ext[1], $exts)) {
                        if ($fs->exists($file)) {
                            $fs->remove($file);
                        }
                    }
                }
            }

            $product->setPicture(null);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Resim başarıyla kaldırıldı');
            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }


        if ($form->isSubmitted() && $form->isValid()) {


            //yeni resim varsa
            if ($form->get('picture')->getData()) {


                //eskisini kontrollü kaldır.
                if ($picOldName && $picOldName != "") {
                    if (strlen($picOldName) > 0) {
                        $fs = new Filesystem();
                        $file = $this->getParameter('products_directory') . '/' . $picOldName;
                        $ext = explode('.', $picOldName);
                        $exts = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
                        if (in_array($ext[1], $exts)) {
                            if ($fs->exists($file)) {
                                $fs->remove($file);
                                $product->setPicture(null);//silme
                            }
                        }
                    }
                }


                //yenisini eklemeye başla
                $file = $form->get('picture')->getData();
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('products_directory'),
                        $fileName
                    );
                    $product->setPicture($fileName);


                } catch (FileException $e) {
                    return new JsonResponse($form->get('picture')->getData());
                }
            } else {
                //yeni resim yoksa, eskisiyle güncelle. (input hidden)
                $product->setPicture($request->request->get('file_name'));
            }

            //özellik ilişkisiz ise boş olarak ekle
            if ($form->get('property')->getData() === 'custom') {
                $product->setCollection(null);
            }


            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Ürün başarıyla güncellendi');

            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }


        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods="DELETE")
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function delete(Request $request, Product $product): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {

            $em = $this->getDoctrine()->getManager();


            if ($product->getPicture() && $product->getPicture() != "") {
                if (strlen($product->getPicture()) > 0) {
                    $fs = new Filesystem();
                    $file = $this->getParameter('products_directory') . '/' . $product->getPicture();
                    $ext = explode('.', $product->getPicture());
                    $exts = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
                    if (in_array($ext[1], $exts)) {
                        if ($fs->exists($file)) {
                            $fs->remove($file);
                        }
                    }
                }
            }


            $em->remove($product);
            $em->flush();
            $this->addFlash('success', 'Ürün silme işlemi başarıyla gerçekleşti');

        }


        return $this->redirectToRoute('product_index');
    }
}