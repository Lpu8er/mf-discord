<?php

namespace App\Command;

use App\Service\Discord;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of DiscordBot
 *
 * @author lpu8er
 */
class DiscordBot extends Command {

    protected $discordService = null;
    
    public function __construct(Discord $discordService) {
        $this->discordService = $discordService;
        parent::__construct();
    }

    protected function configure() {
        $this->setName('discord:start');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->discordService->connect();
    }

}
