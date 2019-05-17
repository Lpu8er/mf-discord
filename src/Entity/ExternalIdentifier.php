<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of ExternalIdentifier
 *
 * @author lpu8er
 * @ORM\Entity()
 * @ORM\Table("externalidentifiers")
 */
class ExternalIdentifier {
    const STATUS_ASKED = 'asked';
    const STATUS_VALIDATED = 'validated';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REVOKED = 'revoked';

    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="string")
     */
    private $syskey;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="string")
     */
    private $sysval;
    /**
     * 
     * @ORM\Column(type="string")
     */
    private $status;
    /**
     * 
     * @ORM\Column(type="string", nullable=true)
     */
    private $mcuid = null;

    public function getSyskey(): ?string
    {
        return $this->syskey;
    }

    public function getSysval(): ?string
    {
        return $this->sysval;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getMcuid(): ?string
    {
        return $this->mcuid;
    }

    public function setMcuid(?string $mcuid): self
    {
        $this->mcuid = $mcuid;

        return $this;
    }
}
