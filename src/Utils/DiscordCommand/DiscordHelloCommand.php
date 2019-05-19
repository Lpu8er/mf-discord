<?php
namespace App\Utils\DiscordCommand;

use App\Service\Discord;

/**
 * Description of DiscordHelloCommand
 *
 * @author lpu8er
 */
class DiscordHelloCommand extends DiscordCommand {
    public function help(Discord $discordService) {
        $discordService->talk('`'.$discordService->getPrefix().'hello` '.$discordService->t('just say hello'), $this->data['channel_id']);
    }
    
    public function execute(Discord $discordService) {
        $discordService->talk($discordService->t('Hello World'), $this->data['channel_id']);
    }
}
