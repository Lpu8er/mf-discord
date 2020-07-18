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
        $ht = '`'.$discordService->getPrefix().'iam` '.$discordService->t('gives you a role among the ones available').':';
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
        if (1 <= count($this->args)) {
            $roleRepo = $discordService->getEntityManager()->getRepository(\App\Entity\DiscordRole::class); /** @var \App\Repository\DiscordRoleRepository $roleRepo */
            $sub = trim(preg_replace('`[^a-zA-Z0-9_-]`', '', array_shift($this->args)));
            if(!empty($sub)) {
                $role = $roleRepo->getLinkableRoleByCommand($sub);
                if(!empty($role)) {
                    $currentDiscordUser = $this->getCurrentDiscordUser();
                    if(!empty($currentDiscordUser)) {
                        $discordService->addRole($currentDiscordUser['id'], $role->getRoleid());
                        $discordService->talk($discordService->t('You now have this role'), $this->data['channel_id']);
                    }
                } else {
                    $discordService->talk($discordService->t('Unknown role'), $this->data['channel_id']);
                }
            } else {
                $this->help($discordService);
            }
        } else {
            $this->help($discordService);
        }
    }
}
