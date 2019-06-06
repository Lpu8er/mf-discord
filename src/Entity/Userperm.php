<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Userperm
 *
 * @author lpu8er
 * @ORM\Entity()
 * @ORM\Table("userperms")
 */
class Userperm {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="string")
     */
    private $permission;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="string")
     */
    private $type;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     */
    private $user;

    public function getPermission(): ?string
    {
        return $this->permission;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getUser(): ?int
    {
        return $this->user;
    }
}
