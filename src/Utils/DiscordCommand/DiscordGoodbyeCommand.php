<?php
namespace App\Utils\DiscordCommand;

use App\Service\Discord;

/**
 * Description of DiscordGoodbye
 *
 * @author lpu8er
 */
class DiscordGoodbyeCommand extends DiscordAdmin {
    public function execute(Discord $discordService) {
        $du = (!empty($this->data['user'])
                && !empty($this->data['user']['id'])
                && empty($this->data['user']['bot'])
                && !empty($this->data['user']['username']))? $this->data['user']:null; // username is populated when the user changes it
        if(!empty($du)) {
            $discordService->talk($discordService->t('%u% has left...', ['%u%' => $this->data['user']['username'],]));
        }
    }
}
