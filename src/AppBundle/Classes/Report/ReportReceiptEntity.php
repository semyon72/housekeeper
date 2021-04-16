<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes\Report;

use AppBundle\Classes\Calculator\FixedValueCalculator;
use AppBundle\Entity\Receipt;
use AppBundle\Classes\ReceiptRounder;
use AppBundle\Classes\Report\ReportReceiptAdjustmentEntity;

/**
 * Description of ReportReceiptEntity
 *
 * @author semyon
 */
class ReportReceiptEntity extends AbstractReportEntity {

    /**
     *
     * @var Receipt
     */
    private $receipt = null;
    
    /**
     * 
     * @param Receipt $receipt
     */
    public function __construct(Receipt $receipt, ReceiptRounder $receiptRounder) {
        $this->receipt = $receipt;
        parent::__construct($receiptRounder);
    }
    
    protected function isFixedCalculatedData() {
        return FixedValueCalculator::guid() === $this->receipt->getTariff()->getService()->getCalculator();
    }
    
    protected function getTariffForScaling() {
        return $this->receipt->getTariff();
    }
    
    /**
     * 
     * @return Receipt
     */
    public function getReceipt() {
        return $this->receipt;
    }
    
    /**
     * 
     * @return string
     */
    public function getServiceName() {
        return $this->receipt->getTariff()->getService()->getName();
    }
    
    /**
     * 
     * @return string Returns two digit numbers style
     */
    public function getMonth(){
        return $this->receipt->getDateE()->format('m');
    }
            
    /**
     * 
     * @return string Returns two digit numbers style
     */
    public function getYear(){
        return $this->receipt->getDateE()->format('y');
    }
            
    /**
     * 
     * @return string 
     */
    public function getTotal(){
        return $this->getFormatedNumber($this->receipt->getTotal(),'total');
    }
    
    /**
     * 
     * @return string
     */
    public function getValueBegin(){
        $result = $this->prinableZeroString;
        if ( !$this->isFixedCalculatedData ) {
            $result= $this->getFormatedNumber($this->receipt->getValueB(),'value');
        }
        return $result;
    }
    
    /**
     * 
     * @return string
     */
    public function getValueEnd(){
        $result = $this->prinableZeroString;
        if ( !$this->isFixedCalculatedData ) {
            $result= $this->getFormatedNumber($this->receipt->getValueE(),'value');
        }
        return $result;
    }

    /**
     * 
     * @return string
     */
    public function getValueDiff(){
        $result = $this->prinableZeroString;
        if ( !$this->isFixedCalculatedData ) {
            $result= $this->getFormatedNumber($this->receipt->getValue(),'value');
        }
        return $result;
    }
    
    /**
     * 
     * @return string
     */
    public function getTariff(){
        return $this->prinableZeroString;
    }
    
    /**
     * 
     * @return array Array of ReportReceiptAdjustmentEntity
     */
    public function getAdjustments(){
        $adjustments = [];
        foreach( $this->receipt->getAdjustments() as $adjustment ) {
            $adjustments[$adjustment->getId()] = new ReportReceiptAdjustmentEntity($adjustment, $this->receiptRounder);
        };
        return $adjustments;
    }
    
}
