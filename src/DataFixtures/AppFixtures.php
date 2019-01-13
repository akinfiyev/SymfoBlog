<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Tag;
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
        $this->loadUsers($manager);
        $this->loadArticles($manager);
        $this->loadComments($manager);
        $this->loadLikes($manager);
        $this->loadTags($manager);
        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        $user_blogger = new User();
        $user_blogger->setEmail('blogger@gmail.com')
            ->setUsername('blogger')
            ->setRoles(['ROLE_BLOGGER'])
            ->setPlainPassword('1111');
        $manager->persist($user_blogger);
        $this->addReference('user_blogger', $user_blogger);

        $user_admin = new User();
        $user_admin->setEmail('admin@gmail.com')
            ->setUsername('admin')
            ->setRoles(['ROLE_ADMIN'])
            ->setPlainPassword('admin');
        $manager->persist($user_admin);
        $this->addReference('user_admin', $user_admin);

        $user_test = new User();
        $user_test->setEmail('test@gmail.com')
            ->setUsername('test')
            ->setRoles(['ROLE_USER'])
            ->setPlainPassword('test');
        $manager->persist($user_test);

        for ($i = 0; $i < 15; $i++) {
            $user = new User();
            $user->setEmail('user' . $i . '@gmail.com')
                ->setUsername('user' . $i)
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword('1111');
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
        }
    }

    public function loadArticles(ObjectManager $manager)
    {
        for ($i = 0; $i < 15; $i++) {
            $article = new Article();
            $article->setTitle('Lorem article ' . $i)
                ->setText('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.')
                ->setCreatedAt(new \DateTime())
                ->setAuthor($this->getReference('user_blogger'))
                ->setIsApproved($i % 2 == 0 ? true : false);
            $manager->persist($article);
            $this->addReference('article_' . $i, $article);
        }
    }

    public function loadComments(ObjectManager $manager)
    {
        for ($i = 0; $i < 15; $i++) {
            for ($j = 0; $j < $i; $j++) {
                if (random_int(0, 1)) {
                    $comment = new Comment();
                    $comment->setArticle($this->getReference('article_' . $i))
                        ->setText('Lorem ipsum comment dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.')
                        ->setCreatedAt(new \DateTime())
                        ->setAuthor($this->getReference('user_' . random_int(0, 14)))
                        ->setIsDeleted(false);
                    $manager->persist($comment);
                }
            }
        }
    }

    public function loadLikes(ObjectManager $manager)
    {
        for ($i = 0; $i < 15; $i++) {
            for ($j = 0; $j < $i; $j++) {
                if (random_int(0, 1)) {
                    $like = new UserLike();
                    $like->setArticle($this->getReference('article_' . $i))
                        ->setUser($this->getReference('user_' . $i));
                    $manager->persist($like);
                }
            }
        }
    }

    public function loadTags(ObjectManager $manager)
    {
        $tagsNames = ['футбол', 'symfony', 'blog', 'admin', 'doctor-mom', 'homework', 'server', 'localhost', 'bitcoin', 'eKreative', 'Lektorium', 'friend', 'work', 'hello', 'burger', 'white paper', 'book', 'deal', 'php'];
        foreach ($tagsNames as $tagName) {
            for ($i = 0; $i < 15; $i++) {
                if (random_int(0, 1)) {
                    $article = $this->getReference('article_' . $i);
                    $tag = new Tag();
                    $tag->setName($tagName)
                        ->setArticle($article);
                    $manager->persist($tag);
                }
            }
        }
    }
}
