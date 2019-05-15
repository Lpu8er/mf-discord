<?php
namespace App\Utils\DiscordCommand;

use App\Service\Discord;

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
    
    abstract public function help(Discord $discordService);
    abstract public function execute(Discord $discordService);
}
