<?php

namespace App\Repository;

use App\Entity\Event;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Exception;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository {
    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, Event::class);
    }

    /**
     * 
     * @param int $year
     * @param int $month
     * @return Event[] Description
     */
    public function retrieveForMonth(int $year, int $month) {
        $returns = [];
        $d = DateTime::createFromFormat('Y-m-d', $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-01');
        if(!empty($d)) {
            $fd = $d->format('Y-m-d').' 00:00:00';
            $ld = $d->format('Y-m').'-'.$d->format('t').' 23:59:59';
            $qb = $this->createQueryBuilder('e');
            $returns = $qb->where('e`.published`=1')
                    ->andWhere('e.`enabled`=1')
                    ->andWhere('e.`start`<=:ld')
                    ->andWhere($qb->expr()->orX(
                            'e.`end` is null',
                            'e.`end`>=:fd'
                            ))
                    ->setParameter('ld', $ld)
                    ->setParameter('fd', $fd);
            // parse @
            foreach($returns as $k => $l) {
                $returns[$k]->setContact(
                    preg_replace_callback(
                        '`@([a-zA-Z0-9_]+)([^a-zA-Z0-9_])`isU',
                        [$this, 'replaceCallbackContact'],
                        htmlspecialchars($l->getContact())
                    ));
            }
        }
        return $returns;
    }
    
    /**
     * 
     * @param string[] $matches
     * @return string
     */
    protected function replaceCallbackContact($matches) {
        $forum = $this->getForumIdFromMcPseudo($matches[1]);
        return '<a href="https://www.minefield.fr/forum/index.php?app=members&module=messaging&section=send&do=form&fromMemberID='.$forum['member_id'].'">'.$matches[1].'</a>'.$matches[2];
    }
    
    /**
     * 
     * @TODO
     * @param string $pseudo
     * @return string[]
     */
    protected function getForumIdFromMcPseudo(string $pseudo) {
        return [
            'member_id' => 0,
        ];
    }
    
    /**
     * 
     * @param string $year
     * @param string $month
     * @param Event[] $events
     * @return array
     */
    public function splitEvents(string $year, string $month, $events) {
        $splitted = [];
        try {
            $fd = DateTime::createFromFormat('Y-m-d', $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-01');
            if(!empty($fd)) {
                $nd = intval($fd->format('t'));
                for($i=1; $i<=$nd; $i++) {
                    $splitted[$i] = [];
                }
                if(!empty($events)) {
                    foreach($events as $e) {
                        $sd = DateTime::createFromFormat('Y-m-d H:i:s', $e->getStart());
                        $ed = empty($e->getEnd())? null:DateTime::createFromFormat('Y-m-d H:i:s', $e->getEnd());
                        for($i=1; $i<=$nd; $i++) {
                            $d = DateTime::createFromFormat('Y-m-d H:i:s', $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-'.str_pad($i, 2, '0', STR_PAD_LEFT).' 00:00:00');
                            $ld = clone $d;
                            $ld->add(new DateInterval('P1D'));
                            if($sd < $ld) {
                                if(empty($ed)) {
                                    $splitted[$i][$e->getId()] = $e->getId();
                                } elseif($ed > $d) {
                                    $splitted[$i][$e->getId()] = $e->getId();
                                }
                            }
                        }
                    }
                }
            }
        } catch(Exception $ex){}
        return $splitted;
    }
}
