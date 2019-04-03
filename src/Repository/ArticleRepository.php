<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findAllApprovedArticles(): ?Query
    {
        return $this->createQueryBuilder('article')
            ->where('article.isApproved = true')
            ->orderBy('article.id', 'DESC')
            ->getQuery();
    }

    public function findAllUnapprovedArticles(): ?Query
    {
        return $this->createQueryBuilder('article')
            ->where('article.isApproved = false')
            ->orderBy('article.id', 'DESC')
            ->getQuery();
    }
}
