<?php
namespace App\Utils\DiscordCommand;

/**
 * Description of DiscordHelpCommand
 *
 * @author lpu8er
 */
class DiscordHelpCommand extends DiscordCommand {
    public function help(\App\Service\Discord $discordService) {
        $discordService->talk('`'.$discordService->getPrefix().'help <cmd>` '.$discordService->t('give some help about `%cmd%` command', ['%cmd%' => '<cmd>']), $this->data['channel_id']);
    }
    
    protected function loadHelp($cmd, \App\Service\Discord $discordService) {
        $o = parent::load($cmd, [], $this->data);
        if(!empty($o)) {
            $o->help($discordService);
        } else {
            $discordService->talk($discordService->t('Unimplemented command `%cmd%`', ['%cmd%' => $cmd,]), $this->data['channel_id']);
        }
    }
    
    public function execute(\App\Service\Discord $discordService) {
        if(1 <= count($this->args)) {
            $sub = preg_replace('`[^a-zA-Z0-9]`', '', array_shift($this->args));
            if($discordService->isAllowedCommand($sub)) {
                $sub = $discordService->getAliasedCommand($sub);
                try {
                    $this->loadHelp($sub, $discordService);
                } catch (Exception $ex) {
                    $discordService->getLogger()->critical($ex->getMessage());
                    $discordService->talk($discordService->t('An error occured, please retry later'), $this->data['channel_id']);
                }
            } else {
                $discordService->talk($discordService->t('Unrecognized command `%cmd%`', ['%cmd%' => $sub,]), $this->data['channel_id']);
            }
        } else {
            $msg = [];
            foreach($discordService->getAllowedCommands() as $c) {
                $msg[] = '**'.$c.'**';
            }
            $discordService->talk($discordService->t('Available commands : %list% (use `%cmd%` to have more information about a command)', ['%list%' => implode(', ', $msg), '%cmd%' => $discordService->getPrefix().'help <cmd>',]), $this->data['channel_id']);
        }
    }
}
