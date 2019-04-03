<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\SoftDeleteable;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 * @SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Comment implements \JsonSerializable
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull()
     * @Assert\Length(
     *      min = 5,
     *      max = 255,
     *      minMessage = "Your comment must be at least {{ limit }} characters long",
     *      maxMessage = "Your comment cannot be longer than {{ limit }} characters"
     * )
     *
     */
    private $text;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var Article
     * @ORM\ManyToOne(targetEntity="App\Entity\Article", inversedBy="comments")
     */
    private $article;

    /**
     * @var bool
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt($deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'text' => $this->getText(),
            'author' => $this->getAuthor()->getId(),
            'created_at' => $this->getCreatedAt(),
            'article' => $this->getArticle()->getId()
        ];
    }
}
