<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 * @ORM\Table("tags")
 */
class Tag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue("CUSTOM")
     * @ORM\CustomIdGenerator(class="App\Utils\IdGenerators\TagIdGenerator")
     * @ORM\Column(type="string")
     */
    protected $strkey;

    public function getStrkey(): ?string
    {
        return $this->strkey;
    }
}
