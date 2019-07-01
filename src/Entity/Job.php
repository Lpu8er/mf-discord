<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Job
 *
 * @author lpu8er
 * @ORM\Entity
 * @ORM\Table("jobs")
 */
class Job {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * 
     * @ORM\Column(name="name", type="string", length=200)
     * @var string
     */
    private $name;
    
    /**
     * 
     * @ORM\Column(name="techName", type="string", length=200)
     * @var string
     */
    private $techName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTechName(): ?string
    {
        return $this->techName;
    }

    public function setTechName(string $techName): self
    {
        $this->techName = $techName;

        return $this;
    }
}
