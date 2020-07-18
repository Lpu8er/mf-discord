<?php
namespace App\Utils\DiscordCommand;

use App\Service\Discord;

/**
 * Description of DiscordIamCommand
 *
 * @author lpu8er
 */
class DiscordIamCommand extends DiscordCommand {
    public function help(Discord $discordService) {
        $roleRepo = $discordService->getEntityManager()->getRepository(\App\Entity\DiscordRole::class); /** @var \App\Repository\DiscordRoleRepository $roleRepo */
        $linkableRoles = $roleRepo->getLinkable();
        $ht = '`'.$discordService->getPrefix().'iam` '.$discordService->t('gives you a role among the ones available:');
        if(!empty($linkableRoles)) {
            foreach($linkableRoles as $lr) {
                $ht .= PHP_EOL;
                $ht .= '`'.$discordService->getPrefix().'iam '.$lr->getLinkto().'` : @'.$discordService->getRoleName($lr->getRoleid());
            }
        } else {
            $ht .= ' *'.$discordService->t('none actually').'*';
        }
        $discordService->talk($ht, $this->data['channel_id']);
    }
    
    public function execute(Discord $discordService) {
        $discordService->talk($discordService->t('Hello World'), $this->data['channel_id']);
    }
}
