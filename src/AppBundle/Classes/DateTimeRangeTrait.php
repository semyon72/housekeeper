<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes;

/**
 * Generated: Feb 12, 2019 2:23:25 AM
 * 
 * Description of DateTimeRangeTrait
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
trait DateTimeRangeTrait {
    
    static protected $TIME_RANGE_FIXED_ON = array('day', 'month', 'year');
    
    protected $timeRangeFixedOn = 'month';
    
    
    private function setDefaultTimeRangeFixedOn($dayMonthYear = ''){
        if ( in_array("$dayMonthYear", self::$TIME_RANGE_FIXED_ON) ){
            $this->timeRangeFixedOn = "$dayMonthYear";
        } else {
            throw new Exception ("'$dayMonthYear'".' not valid name of time range.');
        }

        return $this;
    }
    
    private function setTimeRangeForDay(\DateTime $dateBegin, \DateTime $dateEnd){
        $dateBegin->modify('midnight');
        $dateEnd->modify('23:59:59');
        
        return $this;
    }

    private function setTimeRangeForMonth(\DateTime $dateBegin, \DateTime $dateEnd){
        $dateBegin->modify('first day of this month midnight');
        $dateEnd->modify('last day of this month 23:59:59');
        
        return $this;
    }

    private function setTimeRangeForYear(\DateTime $dateBegin, \DateTime $dateEnd){
        $dateBegin->modify('first day of this year midnight');
        $dateEnd->modify('last day of this year 23:59:59');
        
        return $this;
    }
    
    public function getTimeRangeFixedOn(\DateTime $date = null, $dayMonthYear = '' ){
        $dB = new \DateTime();
        if ( !is_null($date) ) {
            $dB = clone $date;
        }
        $dE = clone $dB;
        
        $rangeName = $this->timeRangeFixedOn;
        if ( in_array("$dayMonthYear", self::$TIME_RANGE_FIXED_ON) ){
            $rangeName = "$dayMonthYear";
        }
        $callback= [$this, 'setTimeRangeFor'.ucfirst($rangeName)];
        if (is_callable( $callback ) ){
            call_user_func_array ($callback,[$dB,$dE]);
        } else {
            throw new Exception ('Can\'t call callback function \''.'setTimeRangeFor'.ucfirst($rangeName).'\'.');
        }
        
        return ['begin'=>$dB, 'end'=>$dE];
    }
    

}
