<?php
// src/Controller/BlogController.php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class BlogController extends AbstractController
{
    public function index()
    {
        return $this->render('blog/index.html.twig', [
            'owner' => 'Thomas',
        ]);
    }


    /**
     * @Route("blog/show/{slug}", requirements={"slug"="^[a-z0-9](-?[a-z0-9])*$"}, name="blog_show")
     */

    public function show(string $slug='Article Sans Titre')
    {
        return $this->render('blog/show.html.twig', [
            'title' => ucwords(trim(str_replace('-',' ',$slug))),
        ]);
    }
}
