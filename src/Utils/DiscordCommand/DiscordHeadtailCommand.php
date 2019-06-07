<?php
namespace App\Utils\DiscordCommand;

use App\Service\Discord;

/**
 * Description of DiscordHeadtailCommand
 *
 * @author lpu8er
 */
class DiscordHeadtailCommand extends DiscordCommand {
    public function help(Discord $discordService) {
        $msg = [];
        $msg[] = '`'.$discordService->getPrefix().'headtail` '.$discordService->t('throws a coin');
        $msg[] = '`'.$discordService->getPrefix().'headtail` <n> '.$discordService->t('throws %arg% coins', ['%arg%' => '<n>',]);
        $msg[] = $discordService->t('%h% = head | %t% = tail', ['%h%' => ':pa_head:', '%t%' => ':pa_tail:',]);
        $discordService->talk(implode(PHP_EOL, $msg), $this->data['channel_id']);
    }
    
    public function execute(Discord $discordService) {
        if (1 <= count($this->args)) {
            $sub = min(10, max(1, intval(preg_replace('`[^0-9]`', '', array_shift($this->args)))));
        } else {
            $sub = 1;
        }
        $msg = [];
        for($i = 0; $i < $sub; $i++) {
            $r = !!mt_rand(0, 1);
            $msg[] = ':pa_'.($r? 'head':'tail').':';
        }
        $discordService->talk(implode(' ', $msg), $this->data['channel_id']);
    }
}
