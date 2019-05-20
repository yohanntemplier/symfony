<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Tag;
use App\Form\ArticleSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class BlogController extends AbstractController
{
    /**
     * @Route("/index", name="blog_index")
     */
    public function index()
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();
        if (!$articles){
            throw $this->createNotFoundException(
                "No articles found in article's table."
            );
        }
        $category = new Category();
        $form = $this->createForm(ArticleSearchType::class, $category,
            ['method'=>Request::METHOD_GET]);
        return $this->render('blog/index.html.twig', [
            'owner' => 'Edouard',
            'articles' => $articles,
            'form' => $form->createView(),
        ]);
    }

    /**
    @param string $slug The slugger
     *
     * @Route("/blog/{slug}",
     *     defaults={"slug" = null},
     *     name="blog_show")
     *  @return Response A response instance
     */
    public function show(?string $slug): Response
    {
        if (!$slug)
        {
            throw $this->createNotFoundException('No slug has been sent to find an article in article\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findOneBy(['title'=> mb_strtolower($slug)]);
        if (!$article)
        {
            throw $this->createNotFoundException('No article with '.$slug.' title found in article\'s table.');
        }
        return $this->render('blog/show.html.twig',
            [
                'title' => ucwords(str_replace('-',' ', $slug)),
                'article' => $article,
                'slug' => $slug,
            ]);
    }
    /**
     * @Route("/cat/{name}", name="show_category").
     */
    public function showByCategory(Category $category): Response
    {
        return $this->render('blog/category.html.twig',
            [
                'articles' => $category->getArticles(),
            ]);
    }

    /**
     * Getting a category with a formatted slug for title
     * @Route("blog/tag/{name}", name="show_tag")
     * @ParamConverter("tag", class="App\Entity\Tag")
     */
    public function showByTag(Tag $tag): Response
    {
        return $this->render(
            'blog/tag.html.twig',
            [
                'tag' => $tag
            ]
        );
    }
}