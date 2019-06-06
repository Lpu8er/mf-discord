<?php
namespace App\Utils\DiscordCommand;

use App\Entity\Ticket;
use App\Service\Discord;
use Exception;

/**
 * Description of DiscordTicketCommand
 *
 * @author lpu8er
 */
class DiscordTicketCommand extends DiscordCommand {
    public function help(Discord $discordService) {
        $discordService->talk('`'.$discordService->getPrefix().'ticket` '.$discordService->t('display your current ticket status (works only in DM)'), $this->data['channel_id']);
    }
    
    public function execute(Discord $discordService) {
        $du = $this->getCurrentDiscordUser();
        if(!empty($this->data['guild_id'])) { // not DM
            $discordService->talk($discordService->t('This command works only when sent by a DM to the bot.'), $this->data['channel_id']);
        } elseif(!empty($du)) {
            $mu = $this->checkAuthLink($discordService);
            if(empty($mu)) {
                $discordService->talk($discordService->t('Not linked (yet ?)'), $this->data['channel_id']);
            } else {
                $trep = $discordService->getEntityManager()->getRepository(Ticket::class);
                $t = null;
                try {
                    $t = $trep->findOneBy(['state' => Ticket::STATE_OPEN, 'playerId' => $mu->getId()]);
                } catch(Exception $e) {
                    $discordService->consoleLog('Fail to get ticket for user ID #'.$mu->getId());
                    $t = null;
                }
                if(empty($t)) {
                    $discordService->talk($discordService->t('No open ticket'), $this->data['channel_id']);
                } else {
                    $msg = [
                        $discordService->t('Open ticket found'),
                    ];
                    $msg[] = ':earth_africa: '.$t->getWorld();
                    $msg[] = ':bookmark: '.$t->getType();
                    $msg[] = ':newspaper: '.$t->getMessage();
                    $discordService->talk(implode(PHP_EOL, $msg), $this->data['channel_id']);
                }
            }
        }
    }
}
