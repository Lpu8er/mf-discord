<?php

namespace App\Repository;

use App\Entity\DiscordRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DiscordRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method DiscordRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method DiscordRole[]    findAll()
 * @method DiscordRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscordRoleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DiscordRole::class);
    }
    
    /**
     * 
     * @return DiscordRole[]
     */
    public function getLinkable() {
        $qb = $this->_em->createQueryBuilder('r');
        return $qb->where($qb->expr()->isNotNull("r.linkto"))
                  ->andWhere("r.locked=0")
                  ->getQuery()->getResult();
    }
}
