<?php
namespace App\Utils\DiscordCommand;

use Exception;

/**
 * Description of DiscordAdmin
 *
 * @author lpu8er
 */
abstract class DiscordAdmin extends DiscordCommand {
    protected function __construct(string $name, array $args, array $data, bool $admin = false) {
        parent::__construct($name, $args, $data, $admin);
        if(!$this->isAdmin()) {
            throw new Exception('Unauthorized');
        }
    }
    
    final public function help(\App\Service\Discord $discordService) { } // do nothing
}
