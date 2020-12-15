<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Business;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Traits\Util;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    const CONFIRM = 1;
    const SAVE_AS_DRAFT = 2;
    const SEND_CONFIRMATION_REQUEST = 0;

    use Util;

    /**
     * @Route("/", name="article_index", methods={"GET"})
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findBy([],['id'=>'desc']),
        ]);
    }

    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     * @throws \Exception
     */
    public function new(Request $request,TranslatorInterface $translator): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $article->setCreatedAt(new \DateTime('now'));
            $article->setLastUpdate(new \DateTime('now'));
            $article->setTotalClaps(0);
            $article->setTotalViews(0);
            $article->setConfirm(self::SEND_CONFIRMATION_REQUEST);
            $article->setUser($this->getUser());
            //todo set slug

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('article_added'));
            return $this->redirectToRoute('article_show', ['id'=>$article->getId()]);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     * @param Article $article
     * @return Response
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Article $article
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, Article $article,
                         TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //dump($form);exit;
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $translator->trans('article_updated'));
            return $this->redirectToRoute('article_show', ['id'=>$article->getId()]);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     * @param Request $request
     * @param Article $article
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Request $request, Article $article, TranslatorInterface $translator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('article_deleted'));
        }

        return $this->redirectToRoute('article_index');
    }


    /**
     * @Route("/business/confirm/{id}", name="article_confirm", methods={"GET"})
     * @param Request $request
     * @param $id
     * @param TranslatorInterface $translator
     * @param \Swift_Mailer $mailer
     * @return mixed
     */
    public function confirm(Request $request, $id, TranslatorInterface $translator,
                            \Swift_Mailer $mailer)
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('confirm'.$id  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $article = $em->getRepository(Article::class)->find($id);
            $article->setConfirm(self::CONFIRM);
            $em->flush();

            //yayına alındığına dair e-posta gönderimi
            /*$from = array('edremitkorfezi.iletisim@gmail.com' => 'Edremit Körfezi');
            $message = (new \Swift_Message('İş Yeriniz Yayında!'))
                ->setFrom($from)
                ->setTo($business->getUser()->getEmail())
                ->setBody(
                    $this->renderView(
                        '_email/business_confirmed.html.twig',
                        array(
                            'baslik'=>$business->getName()
                        )
                    ),
                    'text/html'
                );
            $mailer->send($message);*/


            $this->addFlash('success', $translator->trans('article_confirmed'));

        }

        return $this->redirectToRoute('article_show', ['id' => $id]);

    }

    /**
     * @Route("/business/unconfirm/{id}", name="article_unconfirm", methods={"GET"})
     * @param Request $request
     * @param $id
     * @param TranslatorInterface $translator
     * @return mixed
     */
    public function unconfirm(Request $request, $id, TranslatorInterface $translator)
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('unconfirm'.$id  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $article = $em->getRepository(Article::class)->find($id);
            $article->setConfirm(self::SAVE_AS_DRAFT);
            $em->flush();

            //todo yayından kaldırılma sebebi mail olarak gönderilebilir.

            $this->addFlash('success', $translator->trans('article_unconfirmed'));
        }

        return $this->redirectToRoute('article_show', ['id' => $id]);

    }

    /**
     * @Route("/save-as-draft/{id}", name="article_save_as_draft", methods={"GET"})
     * @param Request $request
     * @param $id
     * @param TranslatorInterface $translator
     * @return mixed
     */
    public function saveAsDraft(Request $request, $id, TranslatorInterface $translator)
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('article_save_as_draft'.$id  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $article = $em->getRepository(Article::class)->find($id);
            $article->setConfirm(self::SAVE_AS_DRAFT);
            $em->flush();

            $this->addFlash('success', $translator->trans('article_confirm_cancelled'));
        }

        return $this->redirectToRoute('article_show', ['id' => $id]);

    }

    /**
     * @Route("/send-confirmation-request/{id}", name="article_send_confirmation_request", methods={"GET"})
     * @param Request $request
     * @param $id
     * @param TranslatorInterface $translator
     * @return mixed
     */
    public function sendConfirmationRequest(Request $request, $id, TranslatorInterface $translator)
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $submittedToken = $request->query->get('_token');

        if ($this->isCsrfTokenValid('article_send_confirmation_request'.$id  , $submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $article = $em->getRepository(Article::class)->find($id);
            $article->setConfirm(self::SEND_CONFIRMATION_REQUEST);
            $em->flush();

            $this->addFlash('success', $translator->trans('article_send_confirmation_request'));
        }

        return $this->redirectToRoute('article_show', ['id' => $id]);

    }



}
