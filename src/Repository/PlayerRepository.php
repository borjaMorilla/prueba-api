<?php

namespace App\Repository;

use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Player|null find($id, $lockMode = null, $lockVersion = null)
 * @method Player|null findOneBy(array $criteria, array $orderBy = null)
 * @method Player[]    findAll()
 * @method Player[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    /**
     * @param int $id
     * @param bool $execute
     * @return Player[]|QueryBuilder
     */
    public function findByPosition(int $id, $execute = true)
    {
        $q = $this->createQueryBuilder('pl')
            ->join('pl.positions', 'p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->orderBy('pl.name', 'ASC');

        return $execute ? $q->getQuery()->getResult() : $q;
    }

    /**
     * @param int $id_team
     * @param int $id_position
     * @param bool $execute
     * @return Player[]|QueryBuilder
     */
    public function findByTeamAndPosition(int $id_team, int $id_position, $execute = true)
    {

        /** @var QueryBuilder $q */
        $q = $this->findByPosition($id_position, false);
        $q->andWhere('pl.team = :team_id')->setParameter('team_id', $id_team);

        return $execute ? $q->getQuery()->getResult() : $q;
    }
}
