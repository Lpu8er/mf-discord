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
        $discordService->talk('`'.$discordService->getPrefix().'ff` freefield event', $this->data['channel_id']);
    }
    
    public function execute(Discord $discordService) {
        $discordService->talk(':bat: **FreeField** https://www.minefield.fr/freefield-liberez-votre-creativite/ ', $this->data['channel_id']);
    }
}
