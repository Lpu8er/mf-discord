<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of DiscordUser
 *
 * @author lpu8er
 */
class DiscordUser {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     *
     * @var string 
     * @ORM\Column(type="string", length=200)
     */
    private $discordName;
    
    /**
     *
     * @var int 
     * @ORM\Column(type="integer")
     */
    private $discriminator;
    
    /**
     *
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateAdd = null;
    
    /**
     *
     * @var User
     */
    private $mfuser = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(int $id): self
    {
        $this->id = $id;
        
        return $this;
    }

    public function getDiscordName(): ?string
    {
        return $this->discordName;
    }

    public function setDiscordName(string $discordName): self
    {
        $this->discordName = $discordName;

        return $this;
    }

    public function getDiscriminator(): ?int
    {
        return $this->discriminator;
    }

    public function setDiscriminator(int $discriminator): self
    {
        $this->discriminator = $discriminator;

        return $this;
    }

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateAdd(?\DateTimeInterface $dateAdd): self
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }
    
    public function getMfuser(): ?User
    {
        return $this->mfuser;
    }

    public function setMfuser(?User $mfuser): self
    {
        $this->mfuser = $mfuser;

        return $this;
    }
}
