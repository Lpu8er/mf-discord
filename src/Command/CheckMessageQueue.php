<?php
namespace App\Command;

use App\Entity\MessageQueue;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of CheckMessageQueue
 *
 * @author lpu8er
 */
class CheckMessageQueue extends Command {

    protected $em = null;
    
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure() {
        $this->setName('discord:msg:check');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $r = $this->em->getRepository(MessageQueue::class);
        $processing = $r->findBy(['status' => MessageQueue::STATUS_PROCESS]);
        $output->writeln('Processing :');
        foreach($processing as $m) {
            $this->printMessageQueue($output, $msg);
        }
        $output->writeln('');
        $output->writeln('Queued :');
        $waiting = $r->findBy(['status' => MessageQueue::STATUS_WAITING]);
        foreach($waiting as $m) {
            $this->printMessageQueue($output, $m);
        }
    }
    
    protected function printMessageQueue(OutputInterface $output, MessageQueue $msg) {
        $output->writeln(strval($msg));
    }
}
