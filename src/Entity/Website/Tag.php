<?php

namespace App\Entity\Website;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
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
    
    /**
     * @ORM\Column(type="string")
     */
    protected $label;

    public function getStrkey(): ?string
    {
        return $this->strkey;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }
}
