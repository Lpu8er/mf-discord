<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Event
 *
 * @author lpu8er
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 * @ORM\Table("events")
 */
class Event {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=200)
     */
    protected $name;
    
    /**
     * @ORM\Column(type="text")
     */
    protected $description = '';
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $start;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $end;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = false;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $published = false;
    
    /**
     * @ORM\Column(type="text")
     */
    protected $publicDescription = '';
    
    /**
     * @ORM\Column(type="text")
     */
    protected $contact = '';
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $subscribeMaxDate = null;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $subscriptionEnabled = false;
    
    /**
     * @ORM\Column(type="text")
     */
    protected $link;
    
    /**
     *
     * @ORM\Column(type="integer")
     */
    protected $category;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getPublicDescription(): ?string
    {
        return $this->publicDescription;
    }

    public function setPublicDescription(string $publicDescription): self
    {
        $this->publicDescription = $publicDescription;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getSubscribeMaxDate(): ?\DateTimeInterface
    {
        return $this->subscribeMaxDate;
    }

    public function setSubscribeMaxDate(?\DateTimeInterface $subscribeMaxDate): self
    {
        $this->subscribeMaxDate = $subscribeMaxDate;

        return $this;
    }

    public function getSubscriptionEnabled(): ?bool
    {
        return $this->subscriptionEnabled;
    }

    public function setSubscriptionEnabled(bool $subscriptionEnabled): self
    {
        $this->subscriptionEnabled = $subscriptionEnabled;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getCategory(): ?int
    {
        return $this->category;
    }

    public function setCategory(int $category): self
    {
        $this->category = $category;

        return $this;
    }
}
