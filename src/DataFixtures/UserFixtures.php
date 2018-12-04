<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user_blogger = new User();
        $user_blogger->setEmail('blogger@gmail.com')
            ->setUsername('blogger')
            ->setRoles(['ROLE_BLOGGER'])
            ->setPlainPassword('1111');
        $manager->persist($user_blogger);

        $user_admin = new User();
        $user_admin->setEmail('admin@gmail.com')
            ->setUsername('admin')
            ->setRoles(['ROLE_ADMIN'])
            ->setPlainPassword('admin');
        $manager->persist($user_admin);

        $manager->flush();
    }
}
