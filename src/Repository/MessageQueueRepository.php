<?php

namespace App\Repository;

use App\Entity\MessageQueue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MessageQueue|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageQueue|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageQueue[]    findAll()
 * @method MessageQueue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageQueueRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MessageQueue::class);
    }
    
    /**
     * 
     * @param string $method
     * @param array $args
     * @param array $data
     * @return MessageQueue
     */
    public function register(string $method, array $args = [], array $data = []): MessageQueue {
        $m = new MessageQueue;
        $m->setArgs(['args' => $args, 'data' => $data]);
        $m->setMethod($method);
        $m->setDateQueue(new \DateTime);
        $m->setStatus(MessageQueue::STATUS_WAITING);
        $this->_em->persist($m);
        $this->_em->flush();
        return $m;
    }
    
    /**
     * 
     * @return MessageQueue
     */
    public function findLastToRun(): ?MessageQueue {
        return $this->findOneBy(['status' => MessageQueue::STATUS_WAITING], ['dateQueue' => 'asc']);
    }
    
    /**
     * 
     * @param MessageQueue $m
     */
    public function flagAsProcessing(MessageQueue $m) {
        $m->setStatus(MessageQueue::STATUS_PROCESS);
        $this->_em->persist($m);
        $this->_em->flush();
    }
    
    /**
     * 
     * @param MessageQueue $m
     */
    public function flagAsDone(MessageQueue $m) {
        $m->setStatus(MessageQueue::STATUS_DONE);
        $this->_em->persist($m);
        $this->_em->flush();
    }
}
