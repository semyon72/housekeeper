<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes\Calculator;

use AppBundle\Classes\Calculator\Range\RangeItem;
use AppBundle\Classes\Calculator\Range\RangeItemCollection;
use AppBundle\Entity\TariffValue;

/**
 * Generated: Feb 22, 2019 2:14:44 PM
 * 
 * Description of MultiTariffTotalCalculator
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class MultiTariffTotalCalculator {

    /**
     * Collection of RangeItem-s that represent the corresponding values from 
     * table TariffValues 
     * 
     * @var RangeItemCollection 
     */
    protected $rangeItems = null;
    
    /**
     * RangeItem that represents corresponding value from Tariff.unitValue field.
     * This value will use for extra values. If $this->rangeItems will not cover
     * all the $this->value then for rest of value will using this unitValue.
     *
     * @var RangeItem 
     */
    protected $defaultItem = null;
    
    /**
     * Total value that have to calculate by ranges that covered by $rangeItems
     * and if it will have remainder then this remainder have to calculate using
     * $this->defaultItem.
     *  
     * @var float 
     */
    protected $value = 0;
    
    
    /**
     *
     * @var int
     */
    protected $scale = null;
    
    
    public function __construct() {
        $this->rangeItems = new RangeItemCollection();
    }
    
    /**
     * 
     * @return int
     */
    public function getScale(){
        return $this->scale;
    }
    
    /**
     * 
     * @param null|int $scale
     * @return $this
     */
    public function setScale($scale){
        if (is_null($scale)) { $this->scale = null; }
            else { $this->scale = abs(intval($scale)); }
        return $this;
    }

    /**
     * 
     * @return float
     */
    public function getValue(){
        return $this->value;
    }

    
    private function _getScaledValue($value){
        $result = floatval($value);
        $scale = $this->getScale();
        if ( !is_null($scale) ){
            $result = round($result,$scale);
        }
        return($result);
    }
    
    /**
     * 
     * @param float $value
     * @return $this
     */
    public function setValue($value){
        $this->value = $this->_getScaledValue($value);
        return $this;
    }
    
    /**
     * 
     * @param float $unitValue
     * @return $this
     */
    public function setDefaultItem($unitValue){
        $item = new RangeItem();
        $this->defaultItem= $item->setScale($this->getScale())->setFrom(0)->setTo(null)->setValue($unitValue);
        $this->defaultItem->getName();
        return $this;
    }
    
    /**
     * 
     * @return RangeItem
     */
    public function getDefaultItem(){
        return $this->defaultItem;
    }

    /**
     * 
     * @return RangeItemCollection
     */
    public function getItems(){
        return $this->rangeItems;
    }


    /**
     * 
     * @param TariffValue $tariffValue
     * @return boolean True if success False otherwise
     */
    public function addItem( TariffValue $tariffValue ){
        $item = (new RangeItem())->setScale($this->getScale());
        $item->setFrom($tariffValue->getValueFrom())->setTo($tariffValue->getValueTo())->setValue($tariffValue->getUnitValue());
        return $this->getItems()->add($item);
    }
    
    public function setItems($tariffValues){
        $this->getItems()->clear();
        foreach ($tariffValues as $key=>$tariffValue){
            if ( !$this->addItem($tariffValue) ) {
                break;
            }
        }
        return $this;
    }

    
    public function calculate(){
        $result=0;
        $value = $this->getValue();
        $this->getItems()->sort();
        foreach ($this->getItems()->getItems() as $key=>$item ){
            if ( $value === 0 ) { break; }
            
            $valueTo = $item->getTo();
            if ( is_null($valueTo) ) {
                $valueTo= $this->getValue();
            }
            $valueFrom = $item->getFrom();
            $diff = $valueTo - $valueFrom;
            $rem = $value - $diff;
            if ( $rem < 0 ) { $diff += $rem; }
            $value -= $diff ;
            
            $result +=  $diff * $item->getValue();
        }
        if ( $value > 0 ){
            $result += $value * $this->getDefaultItem()->getValue();
        }
        
        return $this->_getScaledValue( $result );
    }
    
}
