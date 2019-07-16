<?php
namespace App\Security;

use App\Entity\ExternalIdentifier;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Description of MinefieldAuthenticator
 *
 * @author lpu8er
 */
class MinefieldAuthenticator extends AbstractGuardAuthenticator {
    /**
     *
     * @var string 
     */
    protected $authenticatorPath = null;
    
    /**
     *
     * @var string 
     */
    protected $authenticatorPasskey = null;
    
    /**
     *
     * @var string 
     */
    protected $authenticatorCoreKey = null;
    
    /**
     *
     * @var string 
     */
    protected $authenticatorSyskey = null;
    
    /**
     *
     * @var string 
     */
    protected $authenticatorCookieId = null;
    
    /**
     *
     * @var EntityManagerInterface 
     */
    protected $em = null;
    
    /**
     *
     * @var bool
     */
    protected $initiallyLoaded = false;
    
    /**
     *
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface 
     */
    protected $sessionManager = null;
    
    /**
     *
     * @var string
     */
    protected $oldSessionId = null;
    
    /**
     *
     * @var string
     */
    protected $newSessionId = null;
    
    /**
     *
     * @var LoggerInterface 
     */
    protected $logger = null;

    public function __construct(EntityManagerInterface $em, \Symfony\Component\HttpFoundation\Session\SessionInterface $sessionManager, LoggerInterface $logger, $authenticatorPath, $authenticatorPasskey, $authenticatorCoreKey, $authenticatorSyskey, $authenticatorCookieId) {
        $this->em = $em;
        $this->sessionManager = $sessionManager;
        $this->authenticatorPath = $authenticatorPath;
        $this->authenticatorPasskey = $authenticatorPasskey;
        $this->authenticatorCoreKey = $authenticatorCoreKey;
        $this->authenticatorSyskey = $authenticatorSyskey;
        $this->authenticatorCookieId = $authenticatorCookieId;
        $this->logger = $logger;
    }
    
    /**
     * Manage unique lazy loading
     */
    public function initialLoad() {
        if(!$this->initiallyLoaded) {
            define('IPS_'.$this->authenticatorPasskey, true);
            $this->oldSessionId = $this->sessionManager->getId();
            $this->logger->debug('Old session ID = '.$this->oldSessionId);
            $this->logger->debug('New session ID = '.$this->newSessionId);
            $this->sessionManager->setId($this->newSessionId);
            $rsid = @session_id();
            $this->logger->debug('CURRENT SID = '.$this->sessionManager->getId().' ('.$rsid.')');
            require_once $this->authenticatorPath;
            try {
                \IPS\Session\Front::i();
            } catch(\Exception $e) {
                $this->logger->error('ERROR ON initialLoad : '.$e->getMessage());
            } catch(\Error $er) {
                $this->logger->error('PHP ERROR ON initialLoad : '.$er->getMessage());
            }
            $this->initiallyLoaded = true;
        }
        return $this;
    }
    
    /**
     * 
     * @return array|null
     */
    public function getCurrentForumData(): ?array {
        $data = [];
        try {
            foreach(\IPS\Member::loggedIn()->profileFields() as $k => $v) {
                $sk = preg_replace('`^core_p`', '', $k);
                $data[$k] = $v;
                $data[$sk] = $v;
            }
            $data['member_id'] = \IPS\Member::loggedIn()->member_id;
            $data['name'] = \IPS\Member::loggedIn()->name;
        } catch(\Exception $e) {
            $this->logger->error('ERROR ON getCurrentForumData : '.$e->getMessage());
        } catch(\Error $er) {
            $this->logger->error('PHP ERROR ON getCurrentForumData : '.$er->getMessage());
        }
        return $data;
    }
    
    /**
     * 
     * @return string|null
     */
    public function getCurrentForumId(): ?string {
        $d = $this->getCurrentForumData();
        return empty($d)? null:$d['member_id'];
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request) {
        return $request->cookies->has($this->authenticatorCookieId);
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request) {
        $this->newSessionId = $request->cookies->get($this->authenticatorCookieId);
        $this->initialLoad();
        $returns = [];
        try {
            $mf = $this->getCurrentForumData();
            if(!empty($mf) && !empty($mf['member_id'])) {
                $this->logger->debug('Member ID');
                $ext = $this->em->getRepository(ExternalIdentifier::class)->findOneBy([
                    'syskey' => $this->authenticatorSyskey,
                    'sysval' => $credentials['member_id'],
                    'status' => ExternalIdentifier::STATUS_VALIDATED,
                ]);
                if(!empty($ext)) {
                    $this->logger->debug('get MCUID');
                    $mcuid = $ext->getMcuid();
                    if(!empty($mcuid)) {
                        $this->logger->debug('all clear');
                        $returns['mcuid'] = $mcuid;
                    }
                }
            }
        } catch(\Exception $e) { // if anything happens, that means some spice is in there, so silently terminate it.
            $this->logger->error('Get credentials = '.$e->getMessage());
            $returns = [];
        } catch(\Error $er) {
            $this->logger->error('PHP ERROR ON getCredentials : '.$er->getMessage());
            $returns = [];
        }
        $this->logger->debug('til the end');
        return $returns;
    }

    public function getUser($credentials, UserProviderInterface $userProvider) {
        $returns = null;
        $this->initialLoad();
        try {
            $this->logger->debug('Credentials = '.var_export($credentials, true));
            if(!empty($credentials['mcuid'])) {
                $returns = $this->em->getRepository(User::class)->findOneBy(['mcuid' => $credentials['mcuid'],]);
            }
        } catch(\Exception $e) { // if anything happens, that means some spice is in there, so silently terminate it.
            $this->logger->error('Get user = '.$e->getMessage());
        } catch(\Error $er) {
            $this->logger->error('PHP ERROR ON getUser : '.$er->getMessage());
            $returns = [];
        }
        return $returns;
    }

    public function checkCredentials($credentials, UserInterface $user) {
        return !empty($credentials['mcuid']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        return null;
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null) {
        $this->initialLoad();
        $data = [
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe() {
        return false;
    }
}
