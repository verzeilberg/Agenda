<?php
namespace Agenda\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Agenda\Entity\AgendaItem;

class AgendaItemRepository extends EntityRepository
{
    /**
     * Get fastest agenda item
     * @param $type type of activity (exampl. Run or Ride)
     * @param int $workoutType type of workout 1 = Competition 13 = Training
     *
     * @return      array
     */
    public function getFastestAgendaItem($type = null, $workoutType = 1)
    {
        $qb = $this->createQueryBuilder('r');
        $qb->leftJoin('r.activity a');
        $qb->where('a.workoutType = :workOut');
        $qb->andWhere('r.movingTime > 0');
        if (!empty($type)) {
            $qb->andWhere('a.type = :type');
            $qb->setParameter('type', $type);
        }
        $qb->setParameter('workOut', $workoutType);
        $qb->orderBy('r.movingTime', 'ASC');
        $qb->setMaxResults(1);
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result[0];
    }

    /**
     * Create a new Agenda item object
     * @return      object
     */
    public function createAgendaItem()
    {
        return new AgendaItem();
    }

    /**
     * Save agenda item to database
     * @param       $agendaItem object
     * @return bool
     * @throws OptimisticLockException
     */
    public function storeAgendaItem($agendaItem)
    {
        try {
            $this->getEntityManager()->persist($agendaItem);
            $this->getEntityManager()->flush();
            return true;
        } catch (\Doctrine\DBAL\DBALException $e) {
            return false;
        }
    }

    /**
     * Remove agenda item from database
     * @param       $agendaItem object
     * @return bool
     * @throws OptimisticLockException
     */
    public function removeAgendaItem($agendaItem)
    {
        try {
            $this->getEntityManager()->remove($agendaItem);
            $this->getEntityManager()->flush();
            return true;
        } catch (\Doctrine\DBAL\DBALException $e) {
            return false;
        }
    }

    public function returnEntityManager()
    {
        return $this->getEntityManager();
    }
}