<?php
namespace App\Utils\DiscordCommand;

use App\Service\Discord;

/**
 * Description of DiscordWalkingchickenCommand
 *
 * @author lpu8er
 */
class DiscordWalkingchickenCommand extends DiscordCommand {
    public function help(Discord $discordService) {
        $discordService->talk('`'.$discordService->getPrefix().'walkingchicken` '.$discordService->t('tu du tudu tudu du'), $this->data['channel_id']);
    }
    
    public function execute(Discord $discordService) {
        $discordService->talk('https://www.youtube.com/watch?v=xfV1-mLU9ak', $this->data['channel_id']);
    }
}
