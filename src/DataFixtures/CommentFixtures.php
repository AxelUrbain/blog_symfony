<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        foreach(range(1, 200) as $index) {
            $comment = new Comment();
            $comment->setTitle($faker->words($faker->numberBetween(1, 4), $asText = true));
            $comment->setAuthor($faker->name);
            $comment->setArticle($this->getReference('article' . $faker->numberBetween(1, 50)));
            $comment->setMessage($faker->sentence(3, $asText = true));
            $this->addReference('comment' . $index, $comment);
            $manager->persist($comment);
        }

        $manager->flush();

    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return array(
            ArticleFixtures::class,
        );
    }
}
