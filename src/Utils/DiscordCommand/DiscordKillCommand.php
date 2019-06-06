<?php
namespace App\Utils\DiscordCommand;

use App\Service\Discord;

/**
 * Description of DiscordKillCommand
 *
 * @author lpu8er
 */
class DiscordKillCommand extends DiscordAdmin {
    public function execute(Discord $discordService) {
        $discordService->consoleLog('Starting disconnection...');
        if($discordService->isPolite()) {
            $discordService->talk($discordService->t('Received disconnection order...'));
        }
        $discordService->disconnect();
        $discordService->kill();
        $discordService->consoleLog('Disconnected.');
    }
}
