<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of FlavoredUser
 *
 * @author lpu8er
 * @ORM\Entity()
 * @ORM\Table("flavored_users")
 */
class FlavoredUser {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     */
    private $user;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     */
    private $flavor;

    public function getUser(): ?int
    {
        return $this->user;
    }

    public function getFlavor(): ?int
    {
        return $this->flavor;
    }
}
