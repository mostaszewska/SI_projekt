<?php
/**
 * Answer fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Answer;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class AnswerFixtures.
 */
class AnswerFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @param \Doctrine\Persistence\ObjectManager $manager Persistence object manager
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(50, 'answers', function ($i) {
            $answer = new Answer();
            $answer->setText($this->faker->sentence);
            $answer->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $answer->setUpdatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $answer->setFavourite($this->faker->boolean());
            $answer->setAuthor($this->getRandomReference('users'));
            $answer->setTask($this->getRandomReference('tasks'));
            return $answer;
        });

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return array Array of dependencies
     */
    public function getDependencies(): array
    {
        return [TaskFixtures::class, UserFixtures::class];
    }
}