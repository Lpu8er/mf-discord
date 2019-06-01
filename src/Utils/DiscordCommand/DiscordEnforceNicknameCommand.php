<?php
namespace App\Utils\DiscordCommand;

use App\Service\Discord;

/**
 * Description of DiscordEnforceNicknameCommand
 *
 * @author lpu8er
 */
class DiscordEnforceNicknameCommand extends DiscordAdmin {
    public function execute(Discord $discordService) {
        $du = $this->getCurrentDiscordUser();
        if(!empty($du)) {
            $mu = $this->checkAuthLink($discordService);
            if(!empty($mu)) { // identified and linked
                if($mu->getUsername() != $this->getCurrentDiscordNick()) { // something's odd
                    $discordService->renameMember($du['id'], $mu->getUsername());
                }
            }
        }
    }
}
