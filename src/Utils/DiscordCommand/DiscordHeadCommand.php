<?php
namespace App\Utils\DiscordCommand;

use App\Service\Discord;

/**
 * Description of DiscordHeadCommand
 *
 * @author lpu8er
 */
class DiscordHeadCommand extends DiscordCommand {
    public function help(Discord $discordService) {
        $msg = [];
        $msg[] = '`'.$discordService->getPrefix().'head` '.$discordService->t('throws a coin, success only on head');
        $discordService->talk(implode(PHP_EOL, $msg), $this->data['channel_id']);
    }
    
    public function execute(Discord $discordService) {
        $msg = [];
        $r = !!mt_rand(0, 1);
        $msg[] = $discordService->emote('pa_'.($r? 'head':'tail'));
        $msg[] = $discordService->t($r? 'head':'tail');
        if($r) { $msg[] = ':tada:'; }
        else { $msg[] = ':sob:'; }
        $discordService->talk(implode(' ', $msg), $this->data['channel_id']);
    }
}
