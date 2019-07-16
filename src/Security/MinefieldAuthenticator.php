<?php
namespace App\Security;

use App\Entity\ExternalIdentifier;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct(EntityManagerInterface $em, $authenticatorPath, $authenticatorPasskey, $authenticatorCoreKey, $authenticatorSyskey, $authenticatorCookieId) {
        $this->em = $em;
        $this->authenticatorPath = $authenticatorPath;
        $this->authenticatorPasskey = $authenticatorPasskey;
        $this->authenticatorCoreKey = $authenticatorCoreKey;
        $this->authenticatorSyskey = $authenticatorSyskey;
        $this->authenticatorCookieId = $authenticatorCookieId;
    }
    
    /**
     * Manage unique lazy loading
     */
    public function initialLoad() {
        if(!$this->initiallyLoaded) {
            define('IPS_'.$this->authenticatorPasskey, true);
            require_once $this->authenticatorPath;
            \IPS\Session\Front::i();
            $this->initiallyLoaded = true;
        }
        return $this;
    }
    
    /**
     * 
     * @return array|null
     */
    public function getCurrentForumData(): ?array {
        $this->initialLoad();
        return \IPS\Member::loggedIn();
    }
    
    /**
     * 
     * @return string|null
     */
    public function getCurrentForumId(): ?string {
        $this->initialLoad();
        $d = $this->getCurrentForumData();
        return empty($d)? null:$d['member_id'];
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request) {
        $this->initialLoad();
        return $request->cookies->has($this->authenticatorCookieId);
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request) {
        $this->initialLoad();
        $returns = [];
        /* try { */
            $mf = $this->getForumCurrent();
            if(!empty($mf) && !empty($mf['member_id'])) {
                $ext = $this->em->getRepository(ExternalIdentifier::class)->findOneBy([
                    'syskey' => $this->authenticatorSyskey,
                    'sysval' => $credentials['member_id'],
                    'status' => ExternalIdentifier::STATUS_VALIDATED,
                ]);
                if(!empty($ext)) {
                    $mcuid = $ext->getMcuid();
                    if(!empty($mcuid)) {
                        $returns['mcuid'] = $mcuid;
                    }
                }
            }
        /* } catch(\Exception $e) { // if anything happens, that means some spice is in there, so silently terminate it.
            $returns = [];
        } */
        
        return $returns;
    }

    public function getUser($credentials, UserProviderInterface $userProvider) {
        $returns = null;
        $this->initialLoad();
        /* try { */
            if(!empty($credentials['mcuid'])) {
                $returns = $this->em->getRepository(User::class)->findOneBy(['mcuid' => $credentials['mcuid'],]);
            }
        /* } catch(\Exception $e) { // if anything happens, that means some spice is in there, so silently terminate it.
            
        } */
        return $returns;
    }

    public function checkCredentials($credentials, UserInterface $user) {
        $this->initialLoad();
        return !empty($credentials['mcuid']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        $data = [
            'message' => 'Nope',
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
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
