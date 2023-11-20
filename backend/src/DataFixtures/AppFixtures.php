<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\ArticleFactory;
use App\Factory\CommentFactory;
use App\Factory\JobFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::new()
            ->withAttributes([
                'email' => 'superadmin@example.com',
                'password' => 'adminpass',
            ])
            ->promoteRole('ROLE_SUPER_ADMIN')
            ->create();

        UserFactory::new()
            ->withAttributes([
                'email' => 'admin@example.com',
                'password' => 'adminpass',
            ])
            ->promoteRole('ROLE_ADMIN')
            ->create();

        UserFactory::new()
            ->withAttributes([
                'email' => 'moderatoradmin@example.com',
                'password' => 'adminpass',
            ])
            ->promoteRole('ROLE_MODERATOR')
            ->create();
        UserFactory::createMany(20);
        ArticleFactory::createMany(20);
        CommentFactory::createMany(20);
        JobFactory::createMany(20);

        $manager->flush();
    }
}
