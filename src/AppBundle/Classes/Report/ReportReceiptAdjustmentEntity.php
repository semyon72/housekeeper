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

namespace AppBundle\Classes\Report;

use AppBundle\Classes\Report\AbstractReportEntity;
use AppBundle\Classes\ReceiptRounder;
use AppBundle\Entity\ReceiptAdjustment;
use AppBundle\Classes\Calculator\FixedValueCalculator;

/**
 * Description of ReportReceiptAdjustmentEntity
 *
 * Generated: Mar 19, 2020 9:13:51 PM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class ReportReceiptAdjustmentEntity extends AbstractReportEntity {

    /**
     *  
     * @var ReceiptAdjustment 
     */
    protected $adjustment;
    
    public function __construct(ReceiptAdjustment $adjustment, ReceiptRounder $receiptRounder) {
        $this->adjustment = $adjustment;
        parent::__construct($receiptRounder);
    }
    
    protected function getTariffForScaling() {
        return $this->adjustment->getReceipt()->getTariff();
    }

    protected function isFixedCalculatedData() {
        return FixedValueCalculator::guid() === $this->adjustment->getReceipt()->getTariff()->getService()->getCalculator();
    }
    
    
    /**
     * 
     * @return Receipt
     */
    public function getReceipt() {
        return $this->adjustment->getReceipt();
    }
    
    /**
     * 
     * @return string
     */
    public function getServiceName() {
        return $this->getReceipt()->getTariff()->getService()->getName();
    }
    
    /**
     * 
     * @return string Returns two digit numbers style
     */
    public function getMonth(){
        return $this->adjustment->getDateE()->format('m');
    }
            
    /**
     * 
     * @return string Returns two digit numbers style
     */
    public function getYear(){
        return $this->adjustment->getDateE()->format('y');
    }
            
    /**
     * 
     * @return string 
     */
    public function getTotal(){
        return $this->getFormatedNumber($this->adjustment->getTotal(),'total');
    }
    
    /**
     * 
     * @return string
     */
    public function getValueBegin(){
        $result = $this->prinableZeroString;
        if ( !$this->isFixedCalculatedData ) {
            $result= $this->getFormatedNumber($this->adjustment->getValueB(),'value');
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
            $result= $this->getFormatedNumber($this->adjustment->getValueE(),'value');
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
            $result= $this->getFormatedNumber($this->adjustment->getValue(),'value');
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
     * @return string
     */
    public function getNote(){
        return $this->adjustment->getNote();
    }
    
    /**
     * 
     * @return int
     */
    public function getId(){
        return $this->adjustment->getId();
    }

}
