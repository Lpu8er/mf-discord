<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Ticket
 *
 * @author lpu8er
 * @ORM\Entity()
 * @ORM\Table("tickets")
 */
class Ticket {
    const STATE_OPEN = 'open';
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * 
     * @ORM\Column(name="world", type="string", length=20)
     */
    private $world;
    
    /**
     * 
     * @ORM\Column(name="type", type="string", length=20)
     */
    private $type;
    
    /**
     * 
     * @ORM\Column(name="state", type="string", length=20)
     */
    private $state;
    
    /**
     * 
     * @ORM\Column(type="string")
     */
    private $message;
    
    /**
     * 
     * @ORM\Column(name="playerId", type="integer")
     */
    private $playerId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorld(): ?string
    {
        return $this->world;
    }

    public function setWorld(string $world): self
    {
        $this->world = $world;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getPlayerId(): ?int
    {
        return $this->playerId;
    }

    public function setPlayerId(int $playerId): self
    {
        $this->playerId = $playerId;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }
}
