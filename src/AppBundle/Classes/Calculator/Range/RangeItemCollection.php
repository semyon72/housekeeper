<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes\Calculator\Range;

/**
 * Generated: Feb 21, 2019 10:13:57 AM
 * 
 * Description of RangeItemCollection
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class RangeItemCollection  {

    protected $rangeItems = [];
    
    protected $lastError = '';
    
    
    public function clear(){
        $this->setLastError('');
        $this->rangeItems = [];
        return $this;
    }
    
    public function setLastError($error){
        $this->lastError = trim($error);
        return $this;
    }
    
    public function getLastError(){
        return $this->lastError;
    }
    
    
    public function sort(){
        $items = $this->getItems();
        uasort($items,function($aItem,$bItem){
            $result= null;
            $a = $aItem->getFrom(); //value 'From' always at least zerro
            $b = $bItem->getFrom();
            $res = $a - $b;
            if ( $res !== 0 ){
                if ( $res > 0 ) { $result= 1; }
                    else { $result= -1; }
            } else {
                $a = $aItem->getTo(); //value 'To' can be null that means infifnity value
                $b = $bItem->getTo();
                if ( is_null($a) ){
                    if ( is_null($b) ) { $result= 0; } else { $result= 1; }
                } elseif( is_null($b) ){ $result= -1; }
                else {
                    $res = $a - $b;
                    if ( $res > 0 ) { $result= 1; }
                    elseif ( $res < 0 )  { $result= -1; }
                    else { $result = 0; }
                }
            }
            return $result;
        } );
        
        $this->rangeItems = $items;
        return $this;
    }
    
    protected function isIntersected(RangeItem $aItem, RangeItem $bItem){
        $result = false;
        $Ato = $aItem->getTo();
        $Afrom = $aItem->getFrom();
        $Bto = $bItem->getTo();
        $Bfrom = $bItem->getFrom();
        if( is_null($Bto) ) {
            if( is_null($Ato) ) { $result= true; }
                else if ($Ato > $Bfrom) { $result= true; }
        } else {
            if( is_null($Ato) ) { 
                if( $Afrom < $Bto ) { $result = true; }
            } elseif( $Bfrom < $Afrom && $Bto > $Afrom ){
                    $result = true;
            } elseif( $Bto > $Ato && $Bfrom < $Ato ){
                    $result = true;
            } elseif( $Bfrom >= $Afrom && $Bto <= $Ato ){
                    $result = true;
            }
        }

        return $result;
    } 
    
    /**
     * 
     * @param RangeItem $item
     * @return boolean True if success otherwise false
     */
    protected function validateItem(RangeItem $item){
        $result = true;
        $valueTo = $item->getTo();
        if ( is_null($item->getFrom()) ) { $item->setFrom(0); }
        $valueFrom = $item->getFrom();
        if ( !is_null($valueTo) &&  $valueTo < $valueFrom ){
            $this->setLastError('Lowest range cant be more than highest range, or need assign null value to highest range.');
            $result = false;
        }
        return $result;
    }
    
    /**
     * 
     * @param RangeItem $item
     * @return mixed null if success or Object that intersected with $item
     */
    protected function findIntersection(RangeItem $item){
        foreach($this->getItems() as $key=>$aItem){
            if ( $this->isIntersected($aItem, $item) ){
                $this->setLastError('Current item intersect the one of the items that collected already.');
                return $aItem;
            }
        }
        return null;
    }
    
    
    public function getItems(){
        return $this->rangeItems;
    }
    
    
    public function getItem($itemName){
        return isset($this->getItems()[$itemName]) ? $this->getItems()[$itemName] : null;
    }
    
    
    public function removeItem($itemName){
        $item = $this->getItem($itemName);
        if ( !is_null($item) ) {
            unset($this->rangeItems[$itemName]);
        }
        return $this;
    }
    
    /**
     * 
     * @param RangeItem $item
     * @return boolean True if success False otherwise
     */
    public function add(RangeItem $item){
        $result = false;
        if ( $this->validateItem($item) ){
            $intersectedItem = $this->findIntersection($item);
            if ( is_null($intersectedItem) ){
                $this->rangeItems[$item->getName()] = $item;
                $result = true;
            } // else error
        } // else error
        
        return $result;
    }
    
    
    
}
