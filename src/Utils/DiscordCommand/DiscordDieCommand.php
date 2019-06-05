<?php
namespace App\Utils\DiscordCommand;

use App\Service\Discord;

/**
 * Description of DiscordDieCommand
 *
 * @author lpu8er
 */
class DiscordDieCommand extends DiscordCommand {
    public function help(Discord $discordService) {
        $msg = [];
        $msg[] = '`'.$discordService->getPrefix().'die` '.$discordService->t('throws a die');
        $msg[] = '`'.$discordService->getPrefix().'die` <n> '.$discordService->t('throws %arg% 6-faces dice', ['%arg%' => '<n>',]);
        $msg[] = '`'.$discordService->getPrefix().'die` <n> <h> '.$discordService->t('throws %arg% %sec%-faces dice', ['%arg%' => '<n>', '%sec%' => '<h>',]);
        $discordService->talk(implode(PHP_EOL, $msg), $this->data['channel_id']);
    }
    
    public function execute(Discord $discordService) {
        $nb = 1;
        $faces = 6;
        if (1 <= count($this->args)) {
            $nb = min(10, max(1, intval(preg_replace('`[^0-9]`', '', array_shift($this->args)))));
        }
        if (1 <= count($this->args)) {
            $faces = min(100, max(2, intval(preg_replace('`[^0-9]`', '', array_shift($this->args)))));
        }
        $msg = [];
        for($i = 0; $i < $nb; $i++) {
            $r = mt_rand(1, $faces);
            $msg[] = ':game_die: '.$this->numberToEmote($r, strlen(strval($faces)));
        }
        $discordService->talk(implode(PHP_EOL, $msg), $this->data['channel_id']);
    }
    
    /**
     * 
     * @param int $n
     * @return string
     */
    protected function numberToEmote(int $n, int $mx = 1): string {
        $cnts = [
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
        ];
        $mh = array_map('intval', str_split(str_pad(strval($n), $mx, '0', STR_PAD_LEFT)));
        $returns = '';
        foreach($mh as $s) {
            $returns .= array_key_exists($s, $cnts)? (':'.$cnts[$s].': '):'';
        }
        return $returns;
    }
}
