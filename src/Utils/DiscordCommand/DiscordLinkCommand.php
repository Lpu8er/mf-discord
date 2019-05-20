<?php

namespace App\Utils\DiscordCommand;

use App\Entity\User;
use App\Entity\Userperm;
use App\Service\Discord;
use Exception;

/**
 * Description of DiscordLinkCommand
 *
 * @author lpu8er
 */
class DiscordLinkCommand extends DiscordCommand {

    public function help(Discord $discordService) {
        $msg = [
            $discordService->t('`%cmd%` link your discord account with your Minefield account.', ['%cmd%' => $discordService->getPrefix() . 'link <code>',]),
            $discordService->t('Please note that you have to go on the website to generate the `%code%` to enter there.', ['%code%' => '<code>',]),
            $discordService->t('This command works only when sent by a DM to the bot.'),
        ];
        $discordService->talk(implode(PHP_EOL, $msg), $this->data['channel_id']);
    }

    public function execute(Discord $discordService) {
        $currentDiscordUser = $this->getCurrentDiscordUser();
        if (!empty($this->data['guild_id'])) { // not DM
            $msg = [
                $discordService->t('This command works only when sent by a DM to the bot.'),
            ];
            if (1 <= count($this->args)) {
                $sub = preg_replace('`[^a-zA-Z0-9]`', '', array_shift($this->args));
                if (70 < strlen($sub) && 90 > strlen($sub)) {
                    $this->nukeCode($discordService, $sub);
                    $msg[] = $discordService->t('Therefore, we nuked that code from orbit. Please visit the website again in order to get a new code.');
                }
            }
            $discordService->talk(implode(PHP_EOL, $msg), $this->data['channel_id']);
        } elseif (!empty($currentDiscordUser)) {
            if (1 <= count($this->args)) {
                $sub = preg_replace('`[^a-zA-Z0-9]`', '', array_shift($this->args));
                if (70 < strlen($sub) && 90 > strlen($sub)) {
                    $u = null;
                    $userRepo = $discordService->getEntityManager()->getRepository(User::class);
                    try {
                        $u = $userRepo->findOneBy(['discordLinkCode' => $sub,]);
                    } catch (Exception $e) {
                        $this->logger->critical($ex->getMessage());
                        $this->logger->critical($ex->getTraceAsString());
                        $u = null; // reset
                    }
                    if (!empty($u)) { // found it, link it, embrace it
                        if(empty($u->getDiscordId())
                                || ($currentDiscordUser['id'] == $u->getDiscordId())) {
                            $discordService->startTyping($this->data['channel_id']);
                            $discordService->enableDelay();
                            $u->setDiscordId($currentDiscordUser['id']);
                            $u->setDiscordUser($currentDiscordUser['username'] . '#' . $currentDiscordUser['discriminator']);
                            $discordService->getEntityManager()->persist($u);
                            $discordService->getEntityManager()->flush();
                            $discordService->talk($discordService->t('User found ! Linking...'));
                            // setup roles and stuff
                            $this->setupUser($discordService, $currentDiscordUser, $u);
                            $discordService->talk($discordService->t('Linked and setup complete !'));
                            $discordService->flush($this->data['channel_id']);
                        } else {
                            $discordService->consoleLog('Invalid user discord user #'.$currentDiscordUser['id'].' tried to enter code for discord user #'.$u->getDiscordId());
                            $discordService->talk($discordService->t('Invalid code (error type %err%)', ['%err%' => '403',]), $this->data['channel_id']);
                        }
                    } else { // not found, wtf. @TODO add a queue for that
                        $discordService->consoleLog('Invalid code given for discord user #' . $this->data['author']['id']);
                        $discordService->talk($discordService->t('Invalid code (error type %err%)', ['%err%' => '402',]), $this->data['channel_id']);
                    }
                } else { // invalid code
                    $discordService->talk($discordService->t('Invalid code (error type %err%)', ['%err%' => '401',]), $this->data['channel_id']);
                }
            } else {
                $this->help($discordService);
            }
        } // else it's prolly an automated process not smart enough to hide it : don't bother
    }

    protected function setupUser(Discord $discordService, $discordUser, User $user) {
        $discordMemberData = $discordService->getMember($discordUser['id']);
        if (!empty($discordMemberData)) {
            // rename member
            $discordService->renameMember($discordUser['id'], $user->getUsername());

            // start by clearing all roles
            foreach ($discordMemberData['roles'] as $rid) {
                $discordService->removeRole($discordUser['id'], $rid);
            }

            // add the "joueur" role
            $discordRole = $discordService->getRoleId('joueur');
            if (!empty($discordRole)) {
                $discordService->addRole($discordUser['id'], $discordRole);
            }

            // add "rank" role
            $rolesCorresp = [
                1 => 'vagabond',
                2 => 'paysan',
                3 => 'citoyen',
                4 => 'empereur',
                5 => 'chevalier',
                6 => 'gouverneur',
                7 => 'noble',
                14 => 'villageois',
            ]; // @TODO cfg maybe ? it's kinda hardcoded.
            // give appropriate role
            $gid = intval($user->getGroup());
            if (!empty($gid) && !empty($rolesCorresp[$gid])) {
                $discordRole = $discordService->getRoleId($rolesCorresp[$gid]);
                if (!empty($discordRole)) {
                    $discordService->addRole($discordUser['id'], $discordRole);
                }
            }

            // trader ?
            $perm = null;
            try {
                $perm = $discordService->getEntityManager()->getRepository(Userperm::class)->findOneBy(['permission' => 'trader', 'type' => 'misc', 'user' => $user->getId(),]);
            } catch (Exception $e) {
                $this->logger->critical($ex->getMessage());
                $this->logger->critical($ex->getTraceAsString());
                $perm = null;
            }
            if (!empty($perm)) { // yup.
                $discordRole = $discordService->getRoleId('commercant');
                if (!empty($discordRole)) {
                    $discordService->addRole($discordUser['id'], $discordRole);
                }
            }
        }
    }

    /**
     * 
     * @param Discord $discordService
     * @param string $code
     */
    protected function nukeCode(Discord $discordService, $code) {
        $em = $discordService->getEntityManager();
        $userRepo = $em->getRepository(User::class);
        try {
            $u = $userRepo->findOneBy(['discord_link_code' => $code,]);
            if(!empty($u)) {
                $u->setDiscordLinkCode(null);
                $em->persist($u);
                $em->flush();
            }
        } catch (Exception $e) {
            $discordService->getLogger()->critical($ex->getMessage());
            $discordService->getLogger()->critical($ex->getTraceAsString());
        }
    }
}
