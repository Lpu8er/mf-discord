<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of DiscordRole
 *
 * @author lpu8er
 * @ORM\Entity(repositoryClass="App\Repository\DiscordRoleRepository")
 * @ORM\Table("discord_roles")
 */
class DiscordRole {
    /**
     * @ORM\Id()
     * @ORM\Column(name="roleid", type="string", length=35)
     * @var string
     */
    private $roleid;
    
    /**
     * 
     * @ORM\Column(name="locked", type="boolean")
     * @var bool
     */
    private $locked = false;
    
    /**
     * 
     * @ORM\Column(name="name", type="string", length=200, nullable=true)
     * @var string
     */
    private $linkedto = null;
    
    public function getRoleid(): string {
        return $this->roleid;
    }

    public function getLocked(): bool {
        return $this->locked;
    }

    public function getLinkedto(): ?string {
        return $this->linkedto;
    }
    public function setRoleid(string $roleid) {
        $this->roleid = $roleid;
        return $this;
    }

    public function setLocked(bool $locked) {
        $this->locked = $locked;
        return $this;
    }

    public function setLinkedto(?string $linkedto) {
        $this->linkedto = $linkedto;
        return $this;
    }
}
