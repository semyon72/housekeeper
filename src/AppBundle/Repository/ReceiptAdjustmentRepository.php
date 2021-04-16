<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ReceiptRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReceiptAdjustmentRepository extends EntityRepository {
    
    
    public function findFiltered(array $filter){
        $qb= $this->createQueryBuilder('radj')->select('radj');
        
        $qb->addOrderBy('radj.dateB','DESC');
        $qb->where(' (:dateBegin IS NULL AND :dateEnd IS NULL) '."\r\n".
            'OR ( :dateBegin IS NULL AND :dateEnd IS NOT NULL AND :dateEnd >= radj.dateB ) '."\r\n".
            'OR ( :dateBegin IS NOT NULL AND :dateEnd IS NULL AND :dateBegin <= radj.dateE ) '."\r\n".
            'OR ( :dateBegin IS NOT NULL AND :dateEnd IS NOT NULL AND :dateBegin <= :dateEnd AND '."\r\n".
            '     ( :dateBegin BETWEEN radj.dateB AND radj.dateE '."\r\n".
            '    OR :dateEnd BETWEEN radj.dateB AND radj.dateE '."\r\n".
            '    OR ( :dateBegin <= radj.dateB AND :dateEnd >= radj.dateE ) '."\r\n".
            '      ) '."\r\n".
            '   ) ');
        $qb->setParameters(['dateBegin'=>$filter['dateB'], 'dateEnd'=>$filter['dateE']]);
        $qb->join('radj.receipt','r')->leftJoin('r.tariff','trf');
        if ( !is_null($filter['place']) ) $qb->andWhere($qb->expr()->eq('trf.place',':place'))->setParameter ('place', $filter['place']->getId());
        if ( !is_null($filter['service']) ) $qb->andWhere($qb->expr()->eq('trf.service',':service'))->setParameter ('service', $filter['service']->getId());
        
        return $qb->getQuery()->getResult();
    }
    
    
}