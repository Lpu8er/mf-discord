<?php
namespace App\Utils\DiscordCommand;

use App\Entity\User;
use App\Service\Discord;
use Exception;

/**
 * Description of DiscordCommand
 *
 * @author lpu8er
 */
abstract class DiscordCommand {
    /**
     * 
     * @param string $name
     * @param array $args
     * @param array $data
     * @param bool $admin default as false, used to perform some checks
     * @return DiscordCommand
     */
    final public static function load(string $name, array $args, array $data, bool $admin = false): ?DiscordCommand {
        $returns = null;
        $cls = __NAMESPACE__.'\\Discord'.ucfirst($name).'Command';
        if(class_exists($cls)) {
            $cmdObj = new $cls($name, $args, $data, $admin);
            if(!empty($cmdObj) && is_a($cmdObj, __CLASS__)) {
                $returns = $cmdObj;
            }
        }
        return $returns;
    }
    
    /**
     *
     * @var string
     */
    protected $name;
    
    /**
     *
     * @var array
     */
    protected $args;
    
    /**
     *
     * @var array
     */
    protected $data;
    
    /**
     *
     * @var bool
     */
    protected $admin = false;
    
    protected function __construct(string $name, array $args, array $data, bool $admin = false) {
        $this->name = $name;
        $this->args = $args;
        $this->data = $data;
        $this->admin = $admin;
    }
    
    final public function isAdmin() {
        return $this->admin;
    }
    
    final public function getName() {
        return $this->name;
    }
    
    /**
     * Get current discord user, if any (webhooks and bots excluded)
     * @return array|null
     */
    final protected function getCurrentDiscordUser(): ?array {
        return (!empty($this->data['author'])
                && !empty($this->data['author']['id'])
                && empty($this->data['webhook_id'])
                && empty($this->data['author']['bot']))? $this->data['author']:null;
    }
    
    /**
     * Get current discord nickname, if any (webhooks and bots excluded)
     * @return string|null
     */
    final protected function getCurrentDiscordNick(): ?string {
        return (!empty($this->data['member'])
                && !empty($this->data['member']['user'])
                && !empty($this->data['member']['nick'])
                && empty($this->data['webhook_id']))? $this->data['member']['nick']:null;
    }
    
    /**
     * Get current discord roles
     * @return array|null
     */
    final protected function getCurrentUserRoles(Discord $discordService): ?array {
        $returns = null;
        $u = $this->getCurrentDiscordUser();
        if(!empty($u)) {
            $res = $discordService->getMember($u['id']);
            if(!empty($res)) {
                $returns = $res['roles'];
            }
        }
        return $returns;
    }
    
    /**
     * Get currenly linked user to current discord user if any.
     * @param Discord $discordService
     * @return User
     */
    final protected function checkAuthLink(Discord $discordService): ?User {
        $u = null;
        $cu = $this->getCurrentDiscordUser();
        $userRepo = $discordService->getEntityManager()->getRepository(User::class);
        try {
            $u = $userRepo->findOneBy(['discordId' => $cu['id'],]);
        } catch(Exception $e) {
            $discordService->getLogger()->critical($e->getMessage());
            $discordService->getLogger()->critical($e->getTraceAsString());
            $u = null;
        }
        return $u;
    }
    
    abstract public function help(Discord $discordService);
    abstract public function execute(Discord $discordService);
}
