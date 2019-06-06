<?php

namespace App\Entity\Website;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 * @ORM\Table("articles")
 */
class Article
{
    const STATUS_DRAFT = 'd';
    const STATUS_READY = 'r';
    const STATUS_PUBLISHED = 'p';
    const STATUS_ARCHIEVED = 'a';
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $status;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $locked;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateStart;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateReady;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $userReady; // => serveur.user
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $isPlanned; // ready + isPlanned + datePublished = auto-publish
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $datePublished;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $userPublished; // => serveur.user
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateArchieved;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $userArchieved; // => serveur.user
    
    /**
     * @ORM\Column(type="string")
     */
    protected $title;
    
    /**
     * @ORM\Column(type="text")
     */
    protected $fullContent;
    
    /**
     * @ORM\Column(type="text")
     */
    protected $shortContent;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $slug;
    
    protected $mainImage; // => asset
    
    protected $tags; // => tags
    
    protected $comments;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): self
    {
        $this->locked = $locked;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateReady(): ?\DateTimeInterface
    {
        return $this->dateReady;
    }

    public function setDateReady(\DateTimeInterface $dateReady): self
    {
        $this->dateReady = $dateReady;

        return $this;
    }

    public function getUserReady(): ?int
    {
        return $this->userReady;
    }

    public function setUserReady(int $userReady): self
    {
        $this->userReady = $userReady;

        return $this;
    }

    public function getIsPlanned(): ?bool
    {
        return $this->isPlanned;
    }

    public function setIsPlanned(bool $isPlanned): self
    {
        $this->isPlanned = $isPlanned;

        return $this;
    }

    public function getDatePublished(): ?\DateTimeInterface
    {
        return $this->datePublished;
    }

    public function setDatePublished(\DateTimeInterface $datePublished): self
    {
        $this->datePublished = $datePublished;

        return $this;
    }

    public function getUserPublished(): ?int
    {
        return $this->userPublished;
    }

    public function setUserPublished(int $userPublished): self
    {
        $this->userPublished = $userPublished;

        return $this;
    }

    public function getDateArchieved(): ?\DateTimeInterface
    {
        return $this->dateArchieved;
    }

    public function setDateArchieved(\DateTimeInterface $dateArchieved): self
    {
        $this->dateArchieved = $dateArchieved;

        return $this;
    }

    public function getUserArchieved(): ?int
    {
        return $this->userArchieved;
    }

    public function setUserArchieved(int $userArchieved): self
    {
        $this->userArchieved = $userArchieved;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getFullContent(): ?string
    {
        return $this->fullContent;
    }

    public function setFullContent(string $fullContent): self
    {
        $this->fullContent = $fullContent;

        return $this;
    }

    public function getShortContent(): ?string
    {
        return $this->shortContent;
    }

    public function setShortContent(string $shortContent): self
    {
        $this->shortContent = $shortContent;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    } // => comments
}
