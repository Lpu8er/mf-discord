<?php
namespace App\Command;

use App\Entity\MessageQueue;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of PingDiscord
 *
 * @author lpu8er
 */
class PingDiscord extends Command {

    protected $em = null;
    
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure() {
        $this->setName('discord:ping')
                ->addArgument('channel', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Channel ID')
                ->addArgument('message', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Message');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->write('Pinging');
        $this->em->getRepository(MessageQueue::class)->register('ping', [], [
            'channel' => $input->getArgument('channel'),
            'message' => $input->getArgument('message'),
        ]);
        $output->writeln(' pinged.');
    }
}
