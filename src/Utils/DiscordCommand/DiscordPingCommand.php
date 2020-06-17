<?php
namespace App\Utils\DiscordCommand;

use App\Service\Discord;

/**
 * Description of DiscordPingCommand
 *
 * @author lpu8er
 */
class DiscordPingCommand extends DiscordAdmin {
    public function execute(Discord $discordService) {
        $discordService->talk($this->data['message'], $this->data['channel'], empty($this->data['embed'])? []:$this->data['embed']);
    }
}
