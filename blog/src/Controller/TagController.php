<?php

namespace App\Controller;

use App\Repository\TagRepository;
use App\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class TagController
 * @package App\Controller
 * @IsGranted("ROLE_ADMIN")
 */

class TagController extends AbstractController
{
    /**
     * @var TagRepository
     */

    private $repository;
    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/tag", name="tag_index")
     */

    public function index()
    {
        $tags = $this->repository->findAll();
        return $this->render('tag/index.html.twig', [
            'controller_name' => 'TagController',
            'tags' => $tags,
        ]);

    }

    /**
     * @Route("/tag/{name}", name="tag_show")
     * @param Tag $tag
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function show(Tag $tag)

    {
        if (!$tag) {
            throw $this
                ->createNotFoundException('No tag has been sent to find a category in article\'s table.');
        }

        return $this->render('tag/show.html.twig', [
            'controller_name' => 'TagController',
            'tag' => $tag,
        ]);

    }
}
