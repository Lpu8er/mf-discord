<?php

namespace App\Utils\IdGenerators;

use App\Entity\Tag;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

/**
 * Description of TagIdGenerator
 *
 * @author lpu8er
 */
class TagIdGenerator extends AbstractIdGenerator {

    /**
     * 
     * @param EntityManager $em
     * @param Tag $entity
     */
    public function generate(EntityManager $em, $entity) {
        return strtolower( // lower it
                preg_replace('`(\W+)`', '-', // remove non-compatible chars
                        iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', // transform accents
                                preg_replace('`[:cntrl:]`', '', $entity->getLabel()) // remove ctrl chars
                        )
                )
        );
    }

}
