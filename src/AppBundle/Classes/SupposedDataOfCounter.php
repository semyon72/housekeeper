<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;

/**
 * Generated: Jan 28, 2019 11:06:55 PM
 * 
 * Description of SupposedDataOfCounter
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class SupposedDataOfCounter extends CounterCheckerDependentService {
    use DateTimeRangeTrait;
    
    private $data = [];
    
    public function __construct( EntityManagerInterface $entityManager, FormInterface $counterForm, array $data = null ) {
        parent::__construct($entityManager, $counterForm);

        $this->initData();
        if (!is_null($data)) {
            $this->setData($data);
        }
    }
    
    public function initData(\DateTime $lastDate = null, $lastValue = 0.0, \DateTime $currentDate = null, $currentValue = 0.0){
        $this->data = [
            'last' => ['date'=>$lastDate, 'value'=>$lastValue],
            'current' => ['date'=>$currentDate, 'value'=>$currentValue],
        ];
        return $this;
    }
    
    /**
     * Almost like array_merge_recursively for $this->data and passed $data
     * 
     * @param array $data 
     * @return $this
     */
    public function setData(array $data){
        
        foreach ($this->data as $key=>&$value ){
            if ( isset($data[$key]) ){
                foreach($value as $finalKey=>&$finalValue){
                    if (array_key_exists($finalKey, $data[$key])){
                        $finalValue = $data[$key][$finalKey];
                    }
               }
            }
        }
        
        return $this;
    }
    
    public function getData(){
        return $this->data;
    }
    
    protected function getDaysDiff(\DateTime $dateB, \DateTime $dateE){
        $diffInterval = $dateB->diff($dateE);
        if ($diffInterval->days == -99999) {
            $diffInterval->days = false;
        }
        
        return $diffInterval->days;
    }
    
    protected function getSupposedData() {

        $daysDiff = $this->getDaysDiff($this->data['last']['date'],$this->data['current']['date']);
        if ( $daysDiff < 1 ){
            throw new \Exception('Difference between current date and last/previous doesn\'t have mean if less one day.'); 
        }
        
        $valueDiff = round($this->data['current']['value'] - $this->data['last']['value'], 4);
        $valuePerDay = round($valueDiff / $daysDiff,4);

        $midnightOfLastDay = clone $this->getData()['last']['date'];
        $midnightOfLastDay->modify('this day 23:59:59');
        
        $lastDayOfMonth = clone $this->getData()['last']['date'];
        $lastDayOfMonth->modify('last day of this month 23:59:59');
        
        if ( $lastDayOfMonth->getTimestamp() == $midnightOfLastDay->getTimestamp()){
            $lastDayOfMonth->modify('last day of next month 23:59:59');
        }
        
        
        $finalDaysDiff = $this->getDaysDiff($this->data['last']['date'], $lastDayOfMonth);
        
        return ['date'=>$lastDayOfMonth, 'value'=> $this->data['last']['value'] + round($finalDaysDiff * $valuePerDay,4)]; 
    }
    
    //range
    protected function _9336c72c_ced9_4d15_8578_1947a6b28771(SupposedDataOfCounter $self){
        $counter = $this->counterForm->getData();
        $lastCounter = $self->getLastCounter();
        if( !is_null($lastCounter) ){
            $supposedData = $self->setData([
                'last'=>['date'=>$lastCounter->getOnDate(),'value'=>$lastCounter->getValue()],
                'current'=>['date'=>$counter->getOnDate(),'value'=>$counter->getValue()],
            ])->getSupposedData();
            $counter->setOnDate($supposedData['date'])->setValue($supposedData['value']);
        }
        return $self;
    }    
    
    //fixed
    protected function _20fc5e3a_51f6_493f_9c59_04a51ea735ef(SupposedDataOfCounter $self){
        $counter = $this->counterForm->getData();
        
        $dateRange = $this->getTimeRangeFixedOn($counter->getOnDate());;
        $lastCounter = $self->getLastCounter(
                'onDate',
                ':dateBegin <= cntr.onDate AND cntr.onDate <= :dateEnd ',
                ['dateBegin'=>$dateRange['begin'], 'dateEnd'=>$dateRange['end']]
            );
        if( is_null($lastCounter) ){
            $counter->setOnDate($dateRange['end']);
        } else {
            $self->counterForm->addError( new FormError('Recalculation at end of month have not been did, cause record with same time range is exists.') );
        }
        return $self;
    }

    //cumulative
    protected function _db5bf992_22ad_4917_bf13_71da1df21959(SupposedDataOfCounter $self){
        return $self;
    }
    
    
}
