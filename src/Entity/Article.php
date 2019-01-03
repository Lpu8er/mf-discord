<?php

namespace App\Entity;

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
    
    protected $comments; // => comments
}
