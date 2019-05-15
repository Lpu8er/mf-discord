<?php
namespace App\Repository;

use App\Entity\DiscordInvite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Description of DiscordInviteRepository
 *
 * @author lpu8er
 * @method DiscordInvite|null find($id, $lockMode = null, $lockVersion = null)
 * @method DiscordInvite|null findOneBy(array $criteria, array $orderBy = null)
 * @method DiscordInvite[]    findAll()
 * @method DiscordInvite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscordInviteRepository extends ServiceEntityRepository {
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DiscordInvite::class);
    }
    
    public function generate(\App\Entity\User $user, bool $bypass = false): DiscordInvite {
        $di = null;
        if(!$bypass) {
            $di = $this->findOneBy(['user' => $user,]);
        }
        return empty($di)? (new DiscordInvite):$di;
    }
}
