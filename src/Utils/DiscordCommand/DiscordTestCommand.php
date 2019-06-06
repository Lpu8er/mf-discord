<?php
namespace App\Utils\DiscordCommand;

use App\Service\Discord;

/**
 * Description of DiscordTestCommand
 *
 * @author lpu8er
 */
class DiscordTestCommand extends DiscordCommand {
    public function help(Discord $discordService) {
        $discordService->talk('`'.$discordService->getPrefix().'test` test stuff', $this->data['channel_id']);
    }
    
    public function execute(Discord $discordService) {
        $dr = $this->getCurrentUserRoles($discordService);
        if(!empty($this->data['guild_id'])) { // not DM
            $discordService->talk($discordService->t('This command works only when sent by a DM to the bot.'), $this->data['channel_id']);
        } elseif(!empty($dr)) {
            $discordService->talk('Nope.', $this->data['channel_id']);
        } else {
            $discordService->talk('Nope.', $this->data['channel_id']);
        }
    }
}
