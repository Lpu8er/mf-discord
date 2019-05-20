<?php
namespace App\Utils\DiscordCommand;

use App\Service\Discord;

/**
 * Description of DiscordMeCommand
 *
 * @author lpu8er
 */
class DiscordMeCommand extends DiscordCommand {
    public function help(Discord $discordService) {
        $discordService->talk('`'.$discordService->getPrefix().'me` '.$discordService->t('display some information about your account, if linked (works only in DM)'), $this->data['channel_id']);
    }
    
    public function execute(Discord $discordService) {
        $du = $this->getCurrentDiscordUser();
        if(!empty($this->data['guild_id'])) { // not DM
            $discordService->talk($discordService->t('This command works only when sent by a DM to the bot.'), $this->data['channel_id']);
        } elseif(!empty($du)) {
            $mu = $this->checkAuthLink($discordService);
            $msg = [];
            $msg[] = $discordService->t('Discord user').' : `'.$du['username'].'#'.$du['discriminator'].'`';
            if(empty($mu)) {
                $msg[] = $discordService->t('Not linked (yet ?)');
            } else {
                $msg[] = $discordService->t('Linked to %usr%', ['%usr%' => $mu->getUsername(),]);
                $msg[] = 'PA : '.$mu->getMoney();
                $msg[] = $discordService->t('Income/outcome tracking').' : https://www.minefield.fr/money.php';
            }
            $discordService->talk(implode(PHP_EOL, $msg), $this->data['channel_id']);
        }
    }
}
