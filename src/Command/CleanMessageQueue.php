<?php
namespace App\Command;

use App\Entity\MessageQueue;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of CleanMessageQueue
 *
 * @author lpu8er
 */
class CleanMessageQueue extends Command {

    protected $em = null;
    
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure() {
        $this->setName('discord:msg:clean')
                ->addOption('all', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'To clean ALL messagequeue');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $filters = ['status' => MessageQueue::STATUS_DONE];
        if($input->getOption('all')) {
            $filters = ['status' => [MessageQueue::STATUS_DONE, MessageQueue::STATUS_WAITING,]];
        }
        $r = $this->em->getRepository(MessageQueue::class);
        $output->write('Cleaning... ');
        $ls = $r->findBy($filters);
        foreach($ls as $m) {
            $output->write('.');
            $this->em->remove($m);
            $this->em->flush();
            $output->write('.');
        }
        $output->writeln(' clean.');
    }
}
