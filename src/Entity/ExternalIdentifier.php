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
}
