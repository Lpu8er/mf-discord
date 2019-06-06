<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of MessageQueue
 *
 * @author lpu8er
 * @ORM\Entity(repositoryClass="App\Repository\MessageQueueRepository")
 * @ORM\Table("messagequeues")
 */
class MessageQueue {
    const STATUS_WAITING = 0;
    const STATUS_PROCESS = 1;
    const STATUS_DONE = 2;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     *
     * @ORM\Column(type="integer")
     * @var int 
     */
    private $status;
    
    /**
     *
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $dateQueue;
    
    /**
     * 
     * @ORM\Column(type="string", length=200)
     * @var string
     */
    private $method;
    
    /**
     *
     * @ORM\Column(type="array")
     * @var array 
     */
    private $args;
    
    public function __toString() {
        return '['.$this->getDateQueue()->format('c').'] #'.$this->getId().' ('.$this->getMethod().')';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDateQueue(): ?\DateTimeInterface
    {
        return $this->dateQueue;
    }

    public function setDateQueue(\DateTimeInterface $dateQueue): self
    {
        $this->dateQueue = $dateQueue;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getArgs(): ?array
    {
        return $this->args;
    }

    public function setArgs(array $args): self
    {
        $this->args = $args;

        return $this;
    }
}
