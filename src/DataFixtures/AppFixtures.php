<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\UserLike;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
//        users
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

        $this->addReference('user_blogger', $user_blogger);
        $this->addReference('user_admin', $user_admin);

        for ($i = 0; $i <15; $i++)
        {
            $user = new User();
            $user->setEmail('user' . $i . '@gmail.com')
                ->setUsername('user' . $i)
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword('1111');
            $manager->persist($user);

            $this->addReference('user_' . $i, $user);
        }

//        articles
        for ($i = 0; $i <15; $i++)
        {
            $article = new Article();
            $article->setTitle('My article title ' . $i)
                ->setText('My article text ' . $i . '. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.')
                ->setCreatedAt(new \DateTime())
                ->setAuthor($this->getReference('user_blogger'))
                ->setIsApproved($i % 2 == 0 ? true : false);

            $manager->persist($article);
            $this->addReference('article_' . $i, $article);
        }

        for ($i = 0; $i <15; $i++) {
            for ($j = 0; $j < $i; $j++) {
//                comments
                $comment = new Comment();
                $comment->setArticle($this->getReference('article_' . $i))
                    ->setText('Lorem ipsum comment dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.')
                    ->setCreatedAt(new \DateTime())
                    ->setAuthor($this->getReference('user_' . $i));

                $manager->persist($comment);

//                likes
                $like = new UserLike();
                $like->setArticleId($this->getReference('article_' . $i))
                    ->setUserId($this->getReference('user_' . $i));

                $manager->persist($like);
            }
        }

        $manager->flush();
    }
}
