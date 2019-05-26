<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;
use App\Service\Slugify;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [CategoryFixtures::class];
    }
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 50; $i++) {
            $faker = Faker\Factory::create('fr_FR');
            $article = new Article();
            $article->setTitle(mb_strtolower($faker->sentence()));
            $article->setContent(mb_strtolower($faker->sentence()));
            $slugify=new Slugify();
            $slug = $slugify->generate($article->getTitle());
            $article->setSlug($slug);
            $manager->persist($article);
            $article->setCategory($this->getReference('categorie_' . rand(0, 3)));
            $manager->flush();
        }
    }
}
