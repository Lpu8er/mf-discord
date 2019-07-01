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
                    if(mb_check_encoding($currentDiscordUser['username'], 'UTF-8')) {
                        $u = null;
                        $userRepo = $discordService->getEntityManager()->getRepository(User::class);
                        try {
                            $u = $userRepo->findOneBy(['discordLinkCode' => $sub,]);
                        } catch (Exception $e) {
                            $discordService->getLogger()->critical($e->getMessage());
                            $discordService->getLogger()->critical($e->getTraceAsString());
                            $u = null; // reset
                        }
                        if (!empty($u)) { // found it, link it, embrace it
                            if(!empty($u->getUsername())) {
                                if(empty($u->getDiscordId())
                                        || ($currentDiscordUser['id'] == $u->getDiscordId())) {
                                    $discordService->startTyping($this->data['channel_id']);
                                    $discordService->enableDelay();
                                    $u->setDiscordId($currentDiscordUser['id']);
                                    $u->setDiscordUser(preg_replace('`[^A-Z0-9_a-z-]`', '', $currentDiscordUser['username']) . '#' . $currentDiscordUser['discriminator']);
                                    try {
                                        $discordService->getEntityManager()->persist($u);
                                        $discordService->getEntityManager()->flush();
                                        $discordService->talk($discordService->t('User found ! Linking...'));
                                        // setup roles and stuff
                                        $this->setupUser($discordService, $currentDiscordUser, $u);
                                        $discordService->talk($discordService->t('Linked and setup complete !'));
                                        $discordService->flush($this->data['channel_id']);
                                    } catch (Exception $e) {
                                        $discordService->consoleLog('Fail to save rename of discord user #'.$u->getDiscordId());
                                        $discordService->talk($discordService->t('Invalid code (error type %err%)', ['%err%' => '409',]), $this->data['channel_id']);
                                    }
                                } else {
                                    $discordService->consoleLog('Invalid user discord user #'.$currentDiscordUser['id'].' tried to enter code for discord user #'.$u->getDiscordId());
                                    $discordService->talk($discordService->t('Invalid code (error type %err%)', ['%err%' => '403',]), $this->data['channel_id']);
                                }
                            } else {
                                $discordService->talk($discordService->t('Invalid code (error type %err%)', ['%err%' => '407',]), $this->data['channel_id']);
                            }
                        } else { // not found, wtf. @TODO add a queue for that
                            $discordService->consoleLog('Invalid code given for discord user #' . $this->data['author']['id']);
                            $discordService->talk($discordService->t('Invalid code (error type %err%)', ['%err%' => '402',]), $this->data['channel_id']);
                        }
                    } else {
                        $discordService->consoleLog('Invalid encoding attack from discord user #' . $this->data['author']['id']);
                        $discordService->talk($discordService->t('Invalid code (error type %err%)', ['%err%' => '408',]), $this->data['channel_id']);
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
        $discordService->consoleLog('SETUP USER '.$user->getUsername());
        $discordService->consoleLog(var_export($discordMemberData, true));
        if (!empty($discordMemberData)) {
            $discordService->talk($discordService->t('Checking names...'), $this->data['channel_id']);
            // rename member
            $discordService->renameMember($discordUser['id'], $user->getUsername());

            // start by clearing all roles
            $discordService->talk($discordService->t('Clearing roles...'), $this->data['channel_id']);
            foreach ($discordMemberData['roles'] as $rid) {
                $discordService->removeRole($discordUser['id'], $rid);
            }
            
            $autoRoles = $discordService->getAutoRoles();
            
            // add the "joueur" role
            if (!empty($autoRoles['base'])) {
                $discordService->talk($discordService->t('Adding base role...'), $this->data['channel_id']);
                $discordService->addRole($discordUser['id'], $autoRoles['base']);
            }
            
            // add "rank" role
            $gid = intval($user->getGroup());
            if (!empty($gid) && !empty($autoRoles['ranks'][strval($gid)])) {
                $discordService->consoleLog('SETUP USER ROLE('.strval($gid).') TO('.$autoRoles['ranks'][strval($gid)].')');
                $discordService->talk($discordService->t('Adding role for rank %ranknb% ...', ['%ranknb%' => '#'.strval($user->getGroup()),]), $this->data['channel_id']);
                $discordService->addRole($discordUser['id'], $autoRoles['ranks'][strval($gid)]);
            }
            
            // add "job" role
            $jid = intval($user->getJob());
            if (!empty($jid) && !empty($autoRoles['jobs'][strval($jid)])) {
                $discordService->consoleLog('SETUP USER ROLE('.strval($jid).') TO('.$autoRoles['jobs'][strval($jid)].')');
                $discordService->talk($discordService->t('Adding role for job %jobnb% ...', ['%jobnb%' => '#'.strval($user->getJob()),]), $this->data['channel_id']);
                $discordService->addRole($discordUser['id'], $autoRoles['jobs'][strval($jid)]);
            }
            
            // trader ?
            $perm = null;
            try {
                $perm = $discordService->getEntityManager()->getRepository(Userperm::class)->findOneBy(['permission' => 'trader', 'type' => 'misc', 'user' => $user->getId(),]);
            } catch (Exception $e) {
                $discordService->getLogger()->critical($e->getMessage());
                $discordService->getLogger()->critical($e->getTraceAsString());
                $perm = null;
            }
            if (!empty($perm)) { // yup.
                if (!empty($autoRoles['trader'])) {
                    $discordService->talk($discordService->t('Adding trader role...'), $this->data['channel_id']);
                    $discordService->addRole($discordUser['id'], $autoRoles['trader']);
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
            $discordService->getLogger()->critical($e->getMessage());
            $discordService->getLogger()->critical($e->getTraceAsString());
        }
    }
}
