<?php
namespace App\Service;

use App\Entity\User;
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
     * @var string
     */
    protected $linkCodeSalt = null;
    
    /**
     * 
     * @param string $uri
     * @param string $token
     * @param string $scope
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger, $uri, $token, $scope, $guildId, $baseChannel, $maxAge, $linkCodeSalt) {
        $this->em = $em;
        $this->uri = $uri;
        $this->scope = $scope;
        $this->logger = $logger;
        $this->token = $token;
        $this->guildId = $guildId;
        $this->baseChannel = $baseChannel;
        $this->maxAge = $maxAge;
        $this->linkCodeSalt = $linkCodeSalt;
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
    
    /**
     * Prepare a discord link code
     * @param User $user
     * @return string|null if a discord user is already linked, returns null
     */
    public function prepareLinkCode(User $user): ?string {
        $lc = null;
        $du = $user->getDiscordUser();
        if(empty($du)) {
            $lc = $user->getDiscordLinkCode();
            if(empty($lc)) { // generate
                $lc = $this->generatePseudoRandom();
                $user->setDiscordLinkCode($lc);
                $this->em->persist($user);
                $this->em->flush();
            }
        }
        return $lc;
    }
    
    /**
     * Mash random functions.
     * Should not add much in crypto, but enough for unique responses (cron is erasing old codes anyway)
     * @return string
     */
    public function generatePseudoRandom(): string {
        return bin2hex(random_bytes(8)).str_replace('.', '', uniqid(sha1(bin2hex(random_bytes(5)).$this->linkCodeSalt), true));
    }
}
