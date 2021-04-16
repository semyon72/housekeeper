<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes\Calculator;

use AppBundle\Classes\Calculator\MultiTariffTotalCalculator;

/**
 * Generated: Feb 1, 2019 9:07:06 PM
 * 
 * Description of RangeValuesCalculator
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class RangeValuesCalculator extends PlaceServiceCalculatorAbstract{
    
    const START_RECEIPT_NOT_FOUND_ERROR = 'Start record wasn\'t found in "Receipts" for counters with data that have changes in through time.';
    const COUNTER_FOR_RECEIPT_NOT_FOUND_ERROR = 'Counter with value that same as value of end value of last (previous) receipt was not found.';
    const COUNTER_FOR_RECEIPT_MUST_BE_EXACT_ONE_ERROR = 'COUNTER_FOR_RECEIPT_MUST_BE_EXACT_ONE_ERROR *';
    const COUNTER_FOR_END_RANGE_MUST_BE_ERROR = 'COUNTER_FOR_END_RANGE_MUST_BE_ERROR *';
    const COUNTER_RANGE_BEGIN_VALUE_MORE_END_VALUE_ERROR = 'COUNTER_RANGE_BEGIN_VALUE_MORE_END_VALUE_ERROR *';


    static public function description() {
        return '* Defined by counters *';
    }
    
    static public function guid() {
        return '9336c72c-ced9-4d15-8578-1947a6b28771'; //https://www.guidgenerator.com/
    }
    
    
    protected function initializeCounter() {
        //This exclude extra requests to database that get counters by default
        //Real creation of counters are doing in validate function. 
    }
    
    
    /** 
     * {@inheritdoc}
     * @return boolean|$this
     */
    protected function validate() {
        $result=[];
        
        $receipts = $this->getReceipt();
        if ( !is_array($receipts ) || count($receipts ) === 0){
            $this->addErorr(self::START_RECEIPT_NOT_FOUND_ERROR,'START_RECEIPT_NOT_FOUND_ERROR');
            return false;
        } 
        
        $receipt = $receipts[0];
        $criteria=['value'=>$receipts[0]->getValueE()];
        $counters = $this->entityManager->getRepository($this->get('counter')['data_class'])->findBy($criteria, ['onDate'=>'DESC'], 2);
        if ( count($counters) === 0 || count($counters) > 1) {
            if(count($counters) > 1) {
                $this->addErorr(self::COUNTER_FOR_RECEIPT_MUST_BE_EXACT_ONE_ERROR,'COUNTER_FOR_RECEIPT_MUST_BE_EXACT_ONE_ERROR');
            } else {
                $this->addErorr(self::COUNTER_FOR_RECEIPT_NOT_FOUND_ERROR,'COUNTER_FOR_RECEIPT_NOT_FOUND_ERROR');                
            }
            return false;
        }
        
        $result[]= $counters[0]; //Added first appropriate row
        
        $criteria = ['place'=>$counters[0]->getPlace()->getId(), 'service'=>$counters[0]->getService()->getId(), 'onDate'=>$counters[0]->getOnDate()];        
        $qb = $this->entityManager->createQueryBuilder();
        $counters = $qb->select(['c'])->from(get_class($counters[0]), 'c')
                    ->where('c.onDate > :onDate AND c.place = :place AND c.service = :service')
                    ->orderBy('c.onDate','ASC')
                    ->setParameters($criteria)->getQuery()->setMaxResults(1)->execute();
        if( count($counters) === 0 ){
            $this->addErorr(self::COUNTER_FOR_RECEIPT_NOT_FOUND_ERROR,'COUNTER_FOR_RECEIPT_NOT_FOUND_ERROR');
            return false;
        }
        if ( $result[0]->getValue() >= $counters[0]->getValue() ){
            $this->addErorr(self::COUNTER_RANGE_BEGIN_VALUE_MORE_END_VALUE_ERROR,'COUNTER_RANGE_BEGIN_VALUE_MORE_END_VALUE_ERROR');
            return false;
        }
        array_unshift($result, $counters[0]);
        $this->data['counter']['entity'] = $result;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * @return $this
     * @throws \Exception
     */
    protected function prepareResult(){
        $receiptData = $this->get('receipt');
        $prevReceipt= $receiptData['entity'][0];
        $this->result['receipt'] = $prevReceipt;
        
        $counters = $this->getCounter();
        $newReceipt = new $receiptData['data_class']();
        
        $newReceipt->setValueE($counters[0]->getValue())
                ->setDateE($counters[0]->getOnDate())
                ->setValueB($counters[1]->getValue())
                ->setDateB($counters[1]->getOnDate());
        
        $newReceipt->setValue($newReceipt->getValueE() - $newReceipt->getValueB());
        
        //Need get current Tariff
        $criteria = [
                'currentDate'=>$newReceipt->getDateE(),
                'place'=>$prevReceipt->getTariff()->getPlace()->getId(),
                'service'=>$prevReceipt->getTariff()->getService()->getId()
            ];
        $tariff = $this->entityManager->getRepository( get_class($prevReceipt->getTariff()) )->findActiveBy($criteria,null,1);
        if ( count($tariff) === 0) {
            throw new \Exception('Can\'t find appropriate tariff.');
        } else if( count($tariff) > 1 ) {
            throw new \Exception('Was found more than one appropriate tariff.');
        }
        
        $newReceipt->setTariff($tariff[0]['tariff']);
        $totalCalc = (new MultiTariffTotalCalculator())->setScale(4)
                ->setValue($newReceipt->getValue())
                ->setDefaultItem($tariff[0]['tariff']->getUnitValue())
                ->setItems($tariff[0]['tariff']->getTariffValues());
        $calcError = $totalCalc->getItems()->getLastError();
        if( $calcError !== ''){
            throw new \Exception($calcError);
        }
        $newReceipt->setTotal( $totalCalc->calculate() );
        $this->result['receipt_new'] = $newReceipt;  
        
        if ( $this->getPlaceService()->getPlace()->getId() !== $newReceipt->getTariff()->getPlace()->getId() || 
             $this->getPlaceService()->getService()->getId() !== $newReceipt->getTariff()->getService()->getId()  ){
            throw new \Exception('Violation of integrity has been detected.');
        }
        
        return $this;
    }
    

}
