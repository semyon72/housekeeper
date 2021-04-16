<?php

/*
 *  This file is part of the 'housekeeper' project.
 * 
 *  (c) Semyon Mamonov <semyon.mamonov@gmail.com>
 * 
 *  For the full copyright and license information of project, please view the
 *  LICENSE file that was distributed with this source code. But mostly it 
 *  is similar to standart MIT license that probably pointed below.
 */

/*
 * The MIT License
 *
 * Copyright 2020 Semyon Mamonov <semyon.mamonov@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace AppBundle\Classes\ControllerTrait;

use AppBundle\Entity\Receipt;
use AppBundle\Entity\ReceiptAdjustment;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;

/**
 * Description of ReportControllerTrait
 *
 * Generated: Feb 11, 2020 7:02:37 AM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
trait ReportControllerTrait {
    
    
    /**
     * 
     * @param string $sessionKey
     * @param array $objects
     * @return $this
     */
    private function _storeIdsToSession($sessionKey, array $objects){
        array_walk( $objects, function (&$object){ $object = $object->getId(); });
        $session = $this->get('session');
        $session->remove($sessionKey);
        $session->set($sessionKey, $objects);
        return $this;
    }
    
    /**
     * 
     * @param array $adjustmentsId
     * @return array
     */
    private function _restoreIdsReceiptAdjustmentCallback (array $adjustmentsId){
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $query = $qb->select('r','t','a')
                    ->from(Receipt::class, 'r')
                    ->join('r.tariff', 't')
                    ->join('r.adjustments', 'a', Expr\Join::WITH, $qb->expr()->in('a.id', $adjustmentsId))
                    ->getQuery();
        return $query->getResult();
    } 

    /**
     * 
     * @param array $receiptsId
     * @return array
     */
    private function _restoreIdsReceiptCallback (array $receiptsId){
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()->select('r','t','a')->from(Receipt::class, 'r')->leftJoin('r.tariff', 't')->leftJoin('r.adjustments', 'a');
//        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()->select('r','t')->from(Receipt::class, 'r')->leftJoin('r.tariff', 't');
        $query = $qb->andWhere( $qb->expr()->in('r.id', $receiptsId) )->getQuery();
        return $query->getResult();
    } 

    /**
     * 
     * @param string $sessionKey
     * @param callable $callBack 
     * @return array Array of Receipt entities
     */
    private function _restoreIdsFromSession($sessionKey, callable $callBack){
        $result = [];
        
        $session = $this->get('session');
        if ($session->has($sessionKey)){
            $receiptsId = $session->get($sessionKey);
            if (  count($receiptsId) > 0 ){
                $result= $callBack($receiptsId);
            }
        }
        
        return $result;
    }
    
    
    /**
     * 
     * @param string $sessionKey
     * @param array $receipts
     * @return $this
     */
    private function storeReceiptsToSession($sessionKey, array $receipts){
        return $this->_storeIdsToSession($sessionKey, $receipts);
    }
    
    /**
     * 
     * @param string $sessionKey
     * @return array Array of Receipt entities
     */
    private function restoreReceiptsFromSession($sessionKey){
        return $this->_restoreIdsFromSession($sessionKey, [$this,'_restoreIdsReceiptCallback']);
    }
    
    /**
     * 
     * @param string $sessionKey
     * @return array Array of Receipt entities
     */
    private function restoreReceiptAdjustmentsFromSession($sessionKey){
        return $this->_restoreIdsFromSession($sessionKey, [$this,'_restoreIdsReceiptAdjustmentCallback']);
    }
   
}
