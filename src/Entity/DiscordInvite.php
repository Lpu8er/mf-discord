<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of DiscordInvite
 *
 * @author lpu8er
 * @ORM\Entity(repositoryClass="App\Repository\DiscordInviteRepository")
 * @ORM\Table("discordinvites")
 */
class DiscordInvite {
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
    private $lnk;
    
    /**
     *
     * @var User
     */
    private $user;
}
