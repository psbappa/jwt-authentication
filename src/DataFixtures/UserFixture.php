<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('test@gmail.com');
        $user->setPassword('$2y$13$KnApzeic1kwGGl5wd6AmhOIar2i6niakSJLQbV.F/Ca.YbGCiv3wu');
        $user->getRoles(['ROLE_USER']);
        $user->setName('Test');
        $user->setUsername('TestU');
        $user->setPhone('9007279095');
        $user->setAdminOnlyProperty(false);

        $manager->persist($user);
        $manager->flush();
    }
}
