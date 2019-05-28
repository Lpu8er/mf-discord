<?php
namespace App\Utils\DiscordCommand;

use App\Service\Discord;

/**
 * Description of DiscordFfCommand
 *
 * @author lpu8er
 */
class DiscordFfCommand extends DiscordCommand {
    public function help(Discord $discordService) {
        $discordService->talk('`'.$discordService->getPrefix().'ff` '.$discordService->t('no spoil plz'), $this->data['channel_id']);
    }
    
    public function execute(Discord $discordService) {
        $discordService->talk(':bat: **01/06/2019** :bat:', $this->data['channel_id']);
    }
}
