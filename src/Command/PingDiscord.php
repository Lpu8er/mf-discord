<?php
namespace App\Command;

use App\Entity\MessageQueue;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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
                ->addArgument('channel', InputArgument::REQUIRED, 'Channel ID')
                ->addArgument('message', InputArgument::REQUIRED, 'Message')
                ->addArgument('color', InputArgument::OPTIONAL, 'Color');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->write('Pinging');
        $msgData = [
            'channel' => $input->getArgument('channel'),
            'message' => $input->getArgument('message'),
        ];
        if($input->hasArgument('color')) {
            $msgData['embed'] = [
                'title' => 'test embed',
                'description' => $msgData['message'],
                'color' => hexdec($input->getArgument('color')),
            ];
        }
        $this->em->getRepository(MessageQueue::class)->register('ping', [], $msgData);
        $output->writeln(' pinged.');
    }
}
