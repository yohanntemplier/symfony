<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Service\Slugify;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * @var Slugify
     */
    private $slugify;


    /**
     * ArticleFixtures constructor.
     * @param Slugify $slugify
     */
    public function __construct(Slugify $slugify)
    {
        $this->slug = $slugify;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i = 1; $i <= 1000; $i++) {
            $category = new Category();
            $category->setName("category " . $i);
            $manager->persist($category);
            $tag = new Tag();
            $tag->setName("tag " . $i);
            $manager->persist($tag);
            $article = new Article();
            $article->setTitle("article " . $i);
            $article->setSlug($this->slug->generate($article->getTitle()));
            $article->setContent("article " . $i . " content");
            $article->setCategory($category);
            $article->addTag($tag);
            $manager->persist($article);
        }
        $manager->flush();
    }
}
