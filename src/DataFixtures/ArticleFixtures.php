<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        foreach (range(1,50) as $index) {
            $article = new Article();
            $article->setTitle($faker->words($faker->numberBetween(1, 4), $asText = true));
            $article->setAuthor($faker->name);
            $article->setContent($faker->text(700));
            $article->setPublished($faker->boolean);
            $article->setNbViews(0);
            $article->addCategory($this->getReference('Category' . $faker->numberBetween(0, 5)));
            $this->addReference('article' . $index, $article);
            $manager->persist($article);
        }

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return array(
            CategoryFixtures::class,
        );
    }
}
