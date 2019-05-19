<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table("users")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=180, unique=true)
     */
    private $username;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $group;

    /**
     * 
     */
    private $roles = [];
    
    /**
     *
     * @var string 
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $discordLinkCode = null;
    
    /**
     *
     * @var string 
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $discordUser = null;
    
    /**
     *
     * @var string 
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $discordId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getDiscordLinkCode(): ?string
    {
        return $this->discordLinkCode;
    }

    public function setDiscordLinkCode(?string $discordLinkCode): self
    {
        $this->discordLinkCode = $discordLinkCode;

        return $this;
    }

    public function getDiscordUser(): ?string
    {
        return $this->discordUser;
    }

    public function setDiscordUser(?string $discordUser): self
    {
        $this->discordUser = $discordUser;

        return $this;
    }

    public function getDiscordId(): ?string
    {
        return $this->discordId;
    }

    public function setDiscordId(?string $discordId): self
    {
        $this->discordId = $discordId;

        return $this;
    }

    public function getGroup(): ?int
    {
        return $this->group;
    }

    public function setGroup(int $group): self
    {
        $this->group = $group;

        return $this;
    }
}
