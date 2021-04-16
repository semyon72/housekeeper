<?php

namespace AppBundle\Repository;

use AppBundle\Entity;
use Doctrine\ORM\Query\Expr;
use AppBundle\Classes\Doctrine\RepositoryTrait;

/**
 * TariffRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TariffRepository extends \Doctrine\ORM\EntityRepository
{
    use RepositoryTrait;
    
    public function hasChildren($id) {
        $tables = [ 'receipt'=>'tariff_id', 'receipt_adjustment'=>'tariff_id', 'tariff_value'=>'tariff_id'];
        return $this->_hasChildren($id, $tables);
    }
    
    /**
     * It makes the limitations thru additional conditions by date
     *   :currentDate >= trf.dateB AND :currentDate <= trf.dateE OR trf.dateE IS NULL
     * By other words It returns only those rows which active  on now date.
     * 
     * Parameters same to findBy() method.
     *      
     * @param array $criteria Need use 'currentDate' key for filtering by active Date range
     * @param mixed $orderBy
     * @param integer $limit
     * @param integer $offset
     * @return array|null It returns a result set of Tariff rows.
     */
    public function findActiveBy(array $criteria = array(), $orderBy = null, $limit = null, $offset = null) {
        $qb = $this->createQueryBuilder('trf');
        $subselect = '(SELECT COUNT(tv) FROM '.Entity\TariffValue::class.' tv WHERE trf.id = tv.tariff) as tariffValueCnt';
        $qb->select('trf as tariff')->addSelect($subselect);
        
        if ( array_key_exists('currentDate', $criteria) ){
            $currentDate = new \DateTime();
            $qb->where(' :currentDate >= trf.dateB ', ' :currentDate <= trf.dateE OR trf.dateE IS NULL');
            if ( !is_null($criteria['currentDate']) && is_a($criteria['currentDate'], \DateTime::class) ){
                $currentDate = $criteria['currentDate'];
            }
            unset($criteria['currentDate']);
            $qb->setParameter('currentDate', $currentDate);
        }
        
        foreach ( $criteria as $field=>$value) {
            $qb->andWhere($qb->expr()->eq("trf.$field",":$field"))->setParameter($field, $value);
        }
        
        if (!is_null($orderBy) && is_array($orderBy)){
            foreach ( $orderBy as $field=>$order) {
                $qb->addOrderBy(new Expr\OrderBy($field, $order) );
            }
        }
        
        $query = $qb->setFirstResult($offset)->setMaxResults($limit)->getQuery();

        return($query->getResult());
    }
    

}