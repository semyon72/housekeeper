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

namespace AppBundle\Classes;

use Doctrine\ORM\EntityManagerInterface;

use AppBundle\Entity\Tariff;
use AppBundle\Entity\Receipt;
use AppBundle\Entity\ReceiptAdjustment;
use AppBundle\Classes\Calculator\MultiTariffTotalCalculator;
use AppBundle\Classes\ReceiptRounder;
use AppBundle\Classes\ReceiptRounderDatabaseStorage;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Description of AdjustmentCalculator
 *
 *
 *                       [ Receipts to adjusting ]
 *                       |                       |
 *                       |                       |
 *       |-[rec]-|-[rec]-|-[rec]-|-[rec]-|-[rec]-|  
 * ------|---|---|-------|---|---|-------|-------|--|----> time lapse
 *           |               |                      |
 *           |               |                    [now]
 *           |           curTariff ->>> ------------------->>          
 *           |
 *      prevTariff ->>>
 * 
 * 
 * 
 * Generated: Mar 22, 2020 5:56:46 PM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class AdjustmentCalculator {

    /**
     *
     * @var EntityManagerInterface 
     */
    protected $entityManager = null;
    
    /**
     * @var Tariff
     */
    protected $previousTariff = null;
    
    /**
     * @var Tariff
     */
    protected $currentTariff = null;
    
    /**
     * @var array Array of Adjustments
     */
    protected $adjustments = [];
    
    public function __construct(EntityManagerInterface $entityManager, Tariff $tariff = null) {
        $this->entityManager = $entityManager; 
        if ( !is_null($tariff) ){
            $this->loadData($tariff);
        }
    }
    
    /**
     * 
     * @return Tariff
     */
    public function getPreviousTariff () {
       return $this->previousTariff;
    }

    /**
     * 
     * @return Tariff
     */
    public function getCurrentTariff () {
       return $this->currentTariff;
    }

    /**
     * 
     * @param Tariff $tariff
     * @return TariffChangerData
     */
    public function setCurrentTariff (Tariff $tariff) {
        //get previous tariff
        $this->previousTariff = $this->_getPreviousTariffFor($forTariff);
        $this->currentTariff = $tariff;
        $this->resetAdjustments();
        return $this;
    }

    /**
     * Reset array of Adjustments-s into empty state. 
     * 
     * @return TariffChangerData
     */
    public function resetAdjustments(){
        $this->receipts = [];
        return $this;
    } 
    
    /**
     * 
     * @return array Array of Adjustments
     */
    public function getAdjustments () {
       return $this->adjustments;
    }
    
    
    /**
     * Returns interval of time in seconds that must be recalculated and also set
     * value of begin time and end time for $adjustment  
     * 
     * @param ReceiptAdjustment $adjustment
     * @param Tariff $tariff
     * @return int Returns difference between end time and begin time of $tariff
     *  that intersect to corresponding time of 'Receipt' that is property $adjustment.  
     *  
     */
    private function _getRecalculateAndGetInterval(ReceiptAdjustment $adjustment, Tariff $tariff) {
        $rDB = $adjustment->getReceipt()->getDateB();
        $rDE = $adjustment->getReceipt()->getDateE();
        $tDB = $tariff->getDateB();
        $tDE = $tariff->getDateE();
        
        if ( is_null($tDE) || $timeEnd > $rDE->getTimestamp() ){
           $timeEnd = $rDE->getTimestamp(); 
        } else {
            $timeEnd = $tDE->getTimestamp();
        }
        $timeBegin = $tDB->getTimestamp();
        if ( $timeBegin < $rDB->getTimestamp() ){
           $timeBegin = $rDB->getTimestamp(); 
        }
        
        $adjustment->setDateB((new \DateTime())->setTimestamp($timeBegin))
                ->setDateE((new \DateTime())->setTimestamp($timeEnd));
        
        $result = $timeEnd - $timeBegin;
        
        if ( $result < 0 ){
            throw new \Exception("Time end of range can\'t be less than time begin.");
        }
        
        return $result;
    }

    
    /**
     * This method doesn't save the changes that were did on $adjustment to database.
     * For it You must do it manually.
     * 
     * @param Tariff $forTariff If it is null then it will use tariff that was 
     * passed into constructor or $this->loadData() method. Otherwise will be calculated for $forTariff
     * 
     * @param ReceiptAdjustment $adjustment Must be new but linked to corresponding receipt.
     * 
     * @return $this
     * @throws \Exception
     */
    public function calculateAdjustment(ReceiptAdjustment $adjustment, Tariff $forTariff = null){
        //Clear old notes if $adjustment is not new
        $adjustment->setNote('');
        
        if (is_null($forTariff)){
            $forTariff = $this->getCurrentTariff();
        }
        $receipt = $adjustment->getReceipt();
        $rounder = new ReceiptRounder($this->entityManager, new ReceiptRounderDatabaseStorage($this->entityManager));
        $scale = $rounder->load($forTariff->getPlace())->getScaleForTariff($forTariff);
        
        $multiCalc  = (new MultiTariffTotalCalculator())->setScale(4)
                ->setValue($receipt->getValue())
                ->setDefaultItem($forTariff->getUnitValue())
                ->setItems($forTariff->getTariffValues());
        $calcError = $multiCalc->getItems()->getLastError();
        if( $calcError !== ''){
            throw new \Exception($calcError);
        }
        
        $newTotal= $multiCalc->calculate();      

        $diffTotal = $newTotal - $receipt->getTotal();
        $recalcInterval = $this->_getRecalculateAndGetInterval($adjustment, $forTariff);
        $receiptInterval = $receipt->getDateE()->getTimestamp() - $receipt->getDateB()->getTimestamp();
        $intervalMultiplier = $recalcInterval/$receiptInterval;
        $adjustment->setTotal(round($intervalMultiplier*$diffTotal, $scale->getTotalScale()));
        
        $adjustedValue = round($receipt->getValue()*$intervalMultiplier, $scale->getValueScale());
        $adjustment->setValue($adjustedValue);
        
        $adjustment->setValueE($receipt->getValueE())->setValueB($receipt->getValueB() - $adjustedValue);
        
        
        //add comments
        $note = $adjustment->getNote().
                "Interval multiplier [".round($intervalMultiplier,4)."]. ".
                "Total for new tariff [".round($newTotal,4)."]. ".
                "Difference between Total-s [".round($diffTotal,4)."]. ".
                "New value and total are [Interval multiplier]*[value|total].".
                "Range of Values is end value of receipt minus new Value.";
        
        if (strlen($note) > 255) {
            $note = substr($note,0,250).' ....';
        }
        $adjustment->setNote($note);
        
        
        return $this;
    }
    
    
    /**
     * It will add adjustment for passed $receipt. This method doesn't save the 
     * adjustment that was created to inside database. For it You must do it manually. 
     * 
     * @param Receipt $receipt
     * @return TariffChangerData
     */
    public function addReceipt(Receipt $receipt) {
        
        if ($receipt->getTariff()->getId() === $this->getCurrentTariff()->getId() ){
            throw new \Exception('Receipt have adjustment for this tariff already.'.
                    ' If you need recalculate then You can use '.static::class.'::calculateAdjustment() method, directly.');
        }
        
        $adjustment = new ReceiptAdjustment();
        $adjustment->setTariff($this->getCurrentTariff())->setReceipt($receipt);
        $this->calculateAdjustment($adjustment);
        return $this;
    }
    
    /**
     * This is alias for resetAdjustments
     * 
     * @return TariffChangerData
     */
    function resetReceipts() {
        return $this->resetAdjustments();
    }
    
    /**
     * 
     * @return array Array of Receipt-s
     */
    public function getReceipts () {
       $result =[]; 
       
       foreach ( $this->adjustments as $adjustment){
           $result[]= $adjustment->getReceipt();
       }
       
       return $result;
    }

    /**
     * 
     * @param array $receipts Array of Receipt
     * @return TariffChangerData
     */
    public function setReceipts (array $receipts) {
        foreach($receipts as $receipt){
            $this->addReceipt($receipt);
        }
        return $this;
    }

    /**
     * 
     * @param Tariff $currentTariff
     * @return Tariff|null
     */
    protected function _getPreviousTariffFor(Tariff $currentTariff){
        $qb = $this->entityManager->createQueryBuilder();
        $query =  $qb->select('t')->from(Tariff::class, 't')
                ->andWhere('t.place = :place')
                ->andWhere('t.service = :service')
                ->andWhere('t.dateE IS NULL OR t.dateE <= :dateE')
                ->addOrderBy('t.dateE','DESC')->setMaxResults(1)
                ->setParameters([
                    'dateE'=>$currentTariff->getDateB(), 
                    'place'=>$currentTariff->getPlace(),
                    'service'=>$currentTariff->getService()
                ])->getQuery();
        
        $sql = $query->getSQL();
        return $query->getOneOrNullResult();
    }
    
    
    /**
     * 
     * @param Tariff $previousTariff
     * @param Tariff $currentTariff
     * @return array|null Array of Receipt-s
     */
    protected function _getReceiptsFor(Tariff $previousTariff, Tariff $currentTariff){
        $qb = $this->entityManager->createQueryBuilder();
        $qbuery =  $qb->select('r')->from(Receipt::class, 'r')
                ->andWhere('r.tariff = :tariff')
                ->andWhere('r.dateE >= :dateE OR :dateE IS NULL')
                ->andWhere('r.dateB <= :dateB')
                ->addOrderBy('r.dateE','DESC')->setMaxResults(1)
                ->setParameters([
                    'dateE'=>$currentTariff->getDateE(), 
                    'dateB'=>$currentTariff->getDateB(), 
                    'tariff'=>$previousTariff
                ])->getQuery();
        
        $sql = $qbuery->getSQL();
        $result = $qbuery->getResult();
        return count($result) > 0 ? $result : null;
    }

    
    /**
     * 
     * @param Tariff $forTariff New Tariff object 
     * @return TariffChangerData
     */
    public function loadData(Tariff $forTariff) {
        $this->setCurrentTariff($forTariff);
        //get receipts
        $receipts = $this->_getReceiptsFor($prevTariff,$forTariff);
        $this->setReceipts($receipts);

        return $this;
    }
    
    
    
    
    
}
