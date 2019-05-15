<?php
namespace App\Service;

use App\Utils\REST;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Description of DiscordGroom
 *
 * @author lpu8er
 */
class DiscordGroom {
    
    /**
     *
     * @var string
     */
    protected $uri;
    
    /**
     *
     * @var string
     */
    protected $scope;
    
    /**
     *
     * @var EntityManagerInterface 
     */
    protected $em = null;
    
    /**
     *
     * @var LoggerInterface 
     */
    protected $logger = null;
    
    /**
     *
     * @var string
     */
    protected $clientId;
    
    /**
     *
     * @var string
     */
    protected $clientSecret;
    
    /**
     *
     * @var string
     */
    protected $token;
    
    /**
     *
     * @var string
     */
    protected $guildId;
    
    /**
     *
     * @var string
     */
    protected $baseChannel;
    
    /**
     *
     * @var integer
     */
    protected $maxAge = 60;
    
    /**
     * 
     * @param string $uri
     * @param string $token
     * @param string $scope
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger, $uri, $token, $scope, $guildId, $baseChannel, $maxAge) {
        $this->em = $em;
        $this->uri = $uri;
        $this->scope = $scope;
        $this->logger = $logger;
        $this->token = $token;
        $this->guildId = $guildId;
        $this->baseChannel = $baseChannel;
        $this->maxAge = $maxAge;
    }
    
    public function generateSingleInvite(): ?array {
        $returns = null;
        $response = REST::json($this->uri, '/channels/'.$this->baseChannel.'/invites', null, [
            'max_age' => $this->maxAge,
            'max_uses' => 1,
            'temporary' => false,
            'unique' => true,
        ], [
            'Authorization' => 'Bot '.$this->token,
        ]);
        if($response->isValid()) {
            $d = $response->getContent();
            $returns = $d;
        }
        return $returns;
    }
}
