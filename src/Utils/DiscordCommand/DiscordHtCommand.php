<?php
namespace App\Utils\DiscordCommand;

use App\Service\Discord;

/**
 * Description of DiscordHtCommand
 *
 * @author lpu8er
 */
class DiscordHtCommand extends DiscordCommand {
    public function help(Discord $discordService) {
        $msg = [];
        $smg[] = '`'.$discordService->getPrefix().'ht` '.$discordService->t('throws a coin');
        $smg[] = '`'.$discordService->getPrefix().'ht` <n> '.$discordService->t('throws %arg% coins', ['%arg%' => '<n>',]);
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
            $msg[] = '';
        }
        $discordService->talk($discordService->t('Hello World'), $this->data['channel_id']);
    }
}
