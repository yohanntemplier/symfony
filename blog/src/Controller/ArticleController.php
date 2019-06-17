<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Slugify;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAllWithCategoriesAndTags(),
        ]);
    }

    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request, Slugify $slugify, \Swift_Mailer $mailer): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $slug = $slugify->generate($article->getTitle());
            $article->setSlug($slug);
            $author = $this->getUser();
            $article->setAuthor($author);
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Nouvel article ajouté.');

            $bodyMail = $this->renderView(
                'article/mail/notification.html.twig',
                array('article' => $article)
            );
            $message = (new \Swift_Message('Un nouvel article vient d\'être publié !'))
                ->setFrom($this->getParameter('mailer_from'))
                ->setTo('wilder@wildcodeschool.fr')
                ->setBody($bodyMail);
            $mailer->send($message);

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     */
    public function show(Article $article, Slugify $slugify): Response
    {
        $slug = $slugify->generate($article->getTitle());
        $article->setSlug($slug);

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'slug'=> $slug,
            'isFavorite' => $this->getUser()->isFavorite($article)
        ]);
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_AUTHOR")
     */
    public function edit(Request $request, Article $article, Slugify $slugify): Response
    {
        $user = $this->getUser();
        $author = $article->getAuthor();
        if ($user != $author && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('not autorized');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_index', [
                'id' => $article->getId(),
            ]);
        }

        $slug = $slugify->generate($article->getTitle());
        $article->setSlug($slug);

        $this->addFlash('success', 'Article modifié.');

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
            'slug'=>$slug,
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     * @IsGranted("ROLE_AUTHOR")
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        $this->addFlash('danger', 'Article supprimé.');

        return $this->redirectToRoute('article_index');
    }

    /**
     * @Route("/{id}/favorite", name="article_favorite", methods={"GET","POST"})
     */
    public function favorite(Request $request, Article $article, ObjectManager $manager): Response
    {
        if ($this->getUser()->getFavorites()->contains($article)) {
            $this->getUser()->removeFavorite($article)   ;
        }
        else {
            $this->getUser()->addFavorite($article);
        }

        $manager->flush();

        return $this->json([
            'isFavorite' => $this->getUser()->isFavorite($article)
        ]);
    }
}
