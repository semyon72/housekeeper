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

use AppBundle\Classes\ReceiptRounder;
use AppBundle\Classes\Calculator\FixedValueCalculator;

/**
 * Description of AbstractReportEntity
 *
 * Generated: Mar 19, 2020 9:15:17 PM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
abstract class AbstractReportEntity {
    //put your code here
    
    static public $defaultDecimalPoint = null;
    
    protected $prinableZeroString = ' - ';
    
    protected $isFixedCalculatedData = false;
    
    /**
     *
     * @var ReceiptRounder 
     */
    protected $receiptRounder = null;
    
    
    /**
     *     //$this->receipt->getTariff()
     * @param ReceiptRounder $receiptRounder
     */
    public function __construct(ReceiptRounder $receiptRounder) {

        if ( is_null(self::$defaultDecimalPoint) ){
            self::$defaultDecimalPoint = \localeconv()['decimal_point'];
        }
        $this->receiptRounder = $receiptRounder;
        
        $this->isFixedCalculatedData = $this->isFixedCalculatedData();
    }
    
    /**
     * @param float $value 
     * @param string $scaleType Must be either 'value' or 'total'
     * @return $this
     * @throws Exception
     */
    protected function getFormatedNumber($value, $scaleType = 'value'){
        
        if ( !in_array($scaleType, ['value','total'], true) ) {
                throw new \Exception('Parameter must be one of \'value\' or \'total\' strings.');
        }
        
        $result = (string)$value;
        if ( !is_null($this->receiptRounder) ){
            $scale= $this->receiptRounder->getScaleForTariff($this->getTariffForScaling());
            
            if( !is_null($scale) ){
                list($intStr,$fractStr) = explode(self::$defaultDecimalPoint,$result);
                $result = $intStr.self::$defaultDecimalPoint.str_pad(rtrim($fractStr, "0"), $scale->{'get'.ucfirst($scaleType).'Scale'}(),'0');
            }
        }
        
        return $result;
    }
    
    /**
     * 
     * @return \AppBundle\Entity\Tariff This method must return instance of \AppBundle\Entity\Tariff which scale parameters mut be returnde
     * 
     */
    abstract protected function getTariffForScaling();
    
    /**
     * 
     * @return bool This method must return boolean value that means has it fixed calculator or not. 
     * 
     */
    abstract protected function isFixedCalculatedData ();

    
}
