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
        $du = (!empty($this->data['user'])
                && !empty($this->data['user']['id'])
                && empty($this->data['user']['bot'])
                && empty($this->data['nick']) // no nick yet
                && !empty($this->data['user']['username']))? $this->data['user']:null; // username is populated when the user changes it
        if(!empty($du)) {
            $mu = $this->checkAuthLink($discordService, $this->data['user']['id']);
            if(!empty($mu)) { // identified and linked
                if($mu->getUsername() != $du['username']) { // something's odd is going on
                    $discordService->renameMember($du['id'], $mu->getUsername());
                }
            }
        }
    }
}
