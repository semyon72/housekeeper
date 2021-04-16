<?php

namespace AppBundle\Repository;

use AppBundle\Classes\Doctrine\RepositoryTrait;

/**
 * ReceiptRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReceiptRepository extends \Doctrine\ORM\EntityRepository {
    
    use RepositoryTrait;
    
    public function hasChildren($id) {
        $tables = [ 'receipt_adjustment'=>'receipt_id'];
        return $this->_hasChildren($id, $tables);
    }
    
    
    public function findFiltered(array $filter){
        $qb= $this->createQueryBuilder('rcpt')->select('rcpt');
        
        $qb->addOrderBy('rcpt.dateB','DESC');
        $qb->where(' (:dateBegin IS NULL AND :dateEnd IS NULL) '."\r\n".
            'OR ( :dateBegin IS NULL AND :dateEnd IS NOT NULL AND :dateEnd >= rcpt.dateB ) '."\r\n".
            'OR ( :dateBegin IS NOT NULL AND :dateEnd IS NULL AND :dateBegin <= rcpt.dateE ) '."\r\n".
            'OR ( :dateBegin IS NOT NULL AND :dateEnd IS NOT NULL AND :dateBegin <= :dateEnd AND '."\r\n".
            '     ( :dateBegin BETWEEN rcpt.dateB AND rcpt.dateE '."\r\n".
            '    OR :dateEnd BETWEEN rcpt.dateB AND rcpt.dateE '."\r\n".
            '    OR ( :dateBegin <= rcpt.dateB AND :dateEnd >= rcpt.dateE ) '."\r\n".
            '      ) '."\r\n".
            '   ) ');
        $qb->setParameters(['dateBegin'=>$filter['dateB'], 'dateEnd'=>$filter['dateE']]);
        $qb->join('rcpt.tariff','trf');
        if ( !is_null($filter['place']) ) $qb->andWhere($qb->expr()->eq('trf.place',':place'))->setParameter ('place', $filter['place']->getId());
        if ( !is_null($filter['service']) ) $qb->andWhere($qb->expr()->eq('trf.service',':service'))->setParameter ('service', $filter['service']->getId());
        
        return $qb->getQuery()->getResult();        
    }
    
    
}
