<?php
namespace App\Utils\DiscordCommand;

/**
 * Description of DiscordHelpCommand
 *
 * @author lpu8er
 */
class DiscordHelpCommand extends DiscordCommand {
    public function help(\App\Service\Discord $discordService) {
        $discordService->talk('`'.$discordService->getPrefix().'help <cmd>` give some help about `<cmd>` command', $this->data['channel_id']);
    }
    
    protected function loadHelp($cmd, \App\Service\Discord $discordService) {
        $o = parent::load($cmd, [], $this->data);
        if(!empty($o)) {
            $o->help($discordService);
        } else {
            $discordService->talk('Unimplemented command `'.$cmd.'`');
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
                    var_dump($ex->getMessage());
                    $discordService->talk('An error occured, please retry later', $this->data['channel_id']);
                }
            } else {
                $discordService->talk('Unrecognized command `'.$sub.'`');
            }
        } else {
            $msg = [];
            foreach($discordService->getAllowedCommands() as $c) {
                $msg[] = '**'.$c.'**';
            }
            $discordService->talk('Available commands : '.implode(', ', $msg).' (use `'.$discordService->getPrefix().'help <cmd>` to have more information about a command)', $this->data['channel_id']);
        }
    }
}
