<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes\Calculator\Range;

/**
 * Generated: Feb 21, 2019 9:36:43 AM
 * 
 * Description of RangeItem
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class RangeItem {
    //put your code here
    
    protected $rangeFrom = 0.0;
    
    protected $rangeTo = null;
    
    protected $value = 0.0;
    
    protected $name = '';
    
    protected $scale = null;
    

    private function _getScaledValue($value){
        $result = floatval($value);
        $scale = $this->getScale();
        if ( !is_null($scale) ){
            $result = round($result,$scale);
        }
        return($result);
    }
    
    public function setScale($value = null) {
        $this->scale = is_null($value) ? null : abs(intval($value)) ;
        return $this;
    }

    public function getScale() {
        return $this->scale;
    }
    
    public function setFrom($value) {
        $this->rangeFrom = $this->_getScaledValue($value) ;
        return $this;
    }

    public function getFrom() {
        return $this->rangeFrom;
    }

    public function setTo($value) {
        $this->rangeTo = is_null($value) ? null : $this->_getScaledValue($value);
        return $this;
    }

    public function getTo() {
        return $this->rangeTo;
    }

    public function setValue($value) {
        $this->value = $this->_getScaledValue($value) ;
        return $this;
    }

    public function getValue() {
        return $this->value;
    }
    
    public function setName($value = '') {
        if ( empty($value) ) {
            $tStr = $this->getFrom().'|'.$this->getTo().'|'.$this->getValue().'|'.$this->getScale();
            $value = hash_hmac('md5', $tStr , microtime() );
        }
        $this->name = strval($value) ;
        return $this;
    }

    public function getName() {
        $result = trim($this->name);
        if ( empty($result) ) {
            $result = $this->setName()->getName();
        }
        
        return $result;
    }
    
    
    
}
