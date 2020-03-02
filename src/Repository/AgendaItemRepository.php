<?php

namespace Agenda\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Agenda\Entity\AgendaItem;

class AgendaItemRepository extends EntityRepository
{
    /**
     * @param $date
     * @return array
     */
    public function getAgendaItemsByMonth($date)
    {
        $startMonth = clone $date->modify('first day of this month');
        $endMonth = $date->modify('last day of this month');
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.startDate >= :startMonth OR a.endDate >= :endMonth');
        $qb->setParameter('startMonth', $startMonth->format('Y-m-d'));
        $qb->setParameter('endMonth', $endMonth->format('Y-m-d'));
        $qb->orderBy('a.startDate', 'ASC');
        $query = $qb->getQuery();
        $result = $query->getResult();


        $agendaItems = [];
        foreach ($result as $index => $item) {
            $interval = $item->getStartDate()->diff($item->getEndDate());
            if($interval->format('%a') > 0) {
                for ($x = 0; $x <= $interval->format('%a'); $x++) {
                    $startDate = $item->getStartDate();
                    if ($x > 0) {
                        $startDate->modify('+1 day');
                    }
                    $agendaItems[$startDate->format('Ymd')][] = $item;
                }
            } else {
                $agendaItems[$item->getStartDate()->format('Ymd')][] = $item;
            }
        }
        return $agendaItems;
    }

    /**
     * Get agenda items by date
     * @param $date
     * @return array
     */
    public function getAgendaItemsByDay($date)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where(':date BETWEEN a.startDate AND a.endDate');
        $qb->setParameter('date', $date->format('Y-m-d'));
        $qb->orderBy('a.startDate', 'ASC');
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
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