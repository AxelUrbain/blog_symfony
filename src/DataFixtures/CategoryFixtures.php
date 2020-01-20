<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categories = ['Technologie', 'Formation', 'Musique', 'Film', 'Photographie', 'Art'];

        foreach ($categories as $index => $value) {
            $category = new Category();
            $category->setName($value);
            $manager->persist($category);
            $this->addReference('category' . $index, $category);
        }

        $manager->flush();

    }
}
