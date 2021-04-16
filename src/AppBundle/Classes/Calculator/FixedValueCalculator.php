<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes\Calculator;

use AppBundle\Entity\Tariff;
use AppBundle\Classes\DateTimeRangeTrait;
    
/**
 * Generated: Feb 1, 2019 8:58:13 PM
 * 
 * Description of FixedValueCalculator
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class FixedValueCalculator extends PlaceServiceCalculatorAbstract {
    use DateTimeRangeTrait;
    
    const COUNTERS_FOR_CREATE_THE_RECEIPT_WAS_NOT_FOUND_ERROR = 'COUNTERS_FOR_CREATE_THE_RECEIPT_WAS_NOT_FOUND_ERROR *';
    const SAME_TIME_RANGE_WAS_RECEIPTED_ERROR = 'Counter [{{ COUNTER }}] that transformed into month range from "{{ DATE_BEGIN }}" to "{{ DATE_END }}"'."\r\n".
            ' crossing with receipt that exists [{{ RECEIPT }}] yet.';
    const APPROPRIATE_TARIFF_WAS_NOT_FOUND_ERROR = 'APPROPRIATE_TARIFF_WAS_NOT_FOUND_ERROR *';
    
    
    static public function description() {
        return '* Fixed volume per month *';
    }
    
    static public function guid() {
        return '20fc5e3a-51f6-493f-9c59-04a51ea735ef'; //https://www.guidgenerator.com/
    }

    
    protected function initializeReceipt() {
        parent::initializeReceipt();
        
        return $this;
    }
    
    protected function initializeCounter() {
        // if receipt exists then get row where counter.onDate > receipt.dateE
        // otherwise without limitation by onDate

        $placeService = $this->get('place_service')['entity'];
        $criteria = ['place'=>$placeService->getPlace()->getId(), 'service'=>$placeService->getService()->getId()];
        $receipt= $this->get('receipt')['entity'];
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('c', 'p', 's')->from($this->get('counter')['data_class'], 'c')->join('c.place','p')->join('c.service','s')
                ->where('c.place = :place AND c.service = :service ')->orderBy('c.onDate', 'ASC');
        if ( !is_null($receipt) && count($receipt)>0 ){
            $criteria['dateEnd']= $receipt[0]->getDateE();
            $qb->andWhere('c.onDate > :dateEnd');
        }
        $this->data['counter']['entity'] =  $qb->getQuery()->setMaxResults(1)->execute($criteria);
        
        return $this;
    }

    /**
     * 
     * @return boolean True if ****_ERROR is found. False if all is good.
     */
    private function _check_COUNTERS_FOR_CREATE_THE_RECEIPT_WAS_NOT_FOUND_ERROR(){
        $result= true;
        $counter = $this->get('counter')['entity'];
        if ( is_null($counter) || !is_array($counter) || count($counter)===0 ){
            $this->addErorr(self::COUNTERS_FOR_CREATE_THE_RECEIPT_WAS_NOT_FOUND_ERROR, 'COUNTERS_FOR_CREATE_THE_RECEIPT_WAS_NOT_FOUND_ERROR');
        } else {
            $result=false;
        }
        
        return $result;
    }
    
    private function _check_SAME_TIME_RANGE_WAS_RECEIPTED_ERROR(){
        $result = true; //means ****_ERROR is true/exists
        
        $counters = $this->get('counter')['entity'];
        $receipts= $this->get('receipt')['entity'];
        if ( !is_null($receipts) && is_array($receipts) && count($receipts) > 0 ){
            $rangedOnDate = $this->getTimeRangeFixedOn($counters[0]->getOnDate());
            if ( $rangedOnDate['begin']->getTimestamp() <= $receipts[0]->getDateE()->getTimeStamp() ){
                $errorMessage = str_replace(
                        ['{{ COUNTER }}','{{ DATE_BEGIN }}','{{ DATE_END }}','{{ RECEIPT }}'],
                        [$counters[0]->getOnDate()->format('Y-m-d H:i:s'),
                         $rangedOnDate['begin']->format('Y-m-d H:i:s'),$rangedOnDate['end']->format('Y-m-d H:i:s'),
                         $receipts[0]->getDateE()->format('Y-m-d H:i:s'),
                        ],
                        self::SAME_TIME_RANGE_WAS_RECEIPTED_ERROR
                );
                $this->addErorr($errorMessage,'SAME_TIME_RANGE_WAS_RECEIPTED_ERROR');
            } else { $result = false; }
        } else {
            $result = false;
        }

        return $result;
    }

    private function _check_APPROPRIATE_TARIFF_WAS_NOT_FOUND_ERROR(){
        
        $counters = $this->get('counter')['entity'];
        $rangedOnDate = $this->getTimeRangeFixedOn($counters[0]->getOnDate());
        
        $receipts = $this->get('receipt')['entity'];
        if (!is_null($receipts) && is_array($receipts) && count($receipts) > 0) {
            $tariff = $receipts[0]->getTariff();
            if ( is_null($tariff->getDateE()) ){
                if ( $tariff->getDateB()->getTimestamp() < $rangedOnDate['begin']->getTimestamp()   ){
                    return false;
                }
            } else {
                if ( $tariff->getDateE()->getTimestamp() >= $rangedOnDate['end']->getTimestamp() ){
                    return false;
                }
            }
        } else {
            //Need figure out if exists appropriate/active tariff for this counter/service
            //via request to tariff table
            $criteria = [
                'currentDate'=>$counters[0]->getOnDate(),
                'place'=>$counters[0]->getPlace()->getId(),
                'service'=>$counters[0]->getService()->getId()
            ];
            $tariff = $this->entityManager->getRepository(Tariff::class)->findActiveBy($criteria);
            if ( !is_null($tariff) && is_array($tariff) && count($tariff) > 0){
                return false;
            }
        }
        
        $this->addErorr(self::APPROPRIATE_TARIFF_WAS_NOT_FOUND_ERROR, 'APPROPRIATE_TARIFF_WAS_NOT_FOUND_ERROR');
        return true; //means ****_ERROR is true/exists
    }
    
    
    /**
     * {@inheritdoc}
     */
    protected function validate() {
        //We will suppose that fixed data need calculate per month by default
        //but it will be possible for the change by to set protected $timeRangeFixedOn
        //in appropriate value from constant self::TIME_RANGE_FIXED_ON
        
        if ( $this->_check_COUNTERS_FOR_CREATE_THE_RECEIPT_WAS_NOT_FOUND_ERROR() ) { return false; }
        //therefor work counter exists
        
        //Check if exists row on same month
        if ( $this->_check_SAME_TIME_RANGE_WAS_RECEIPTED_ERROR() ) { return false; }
        
        //Check if exists row on same month
        if ( $this->_check_APPROPRIATE_TARIFF_WAS_NOT_FOUND_ERROR() ) { return false; }
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareResult() {
        $receiptData = $this->get('receipt');
        $this->result['receipt']= null;
        if ( !is_null($receiptData['entity']) && count($receiptData['entity'])>0 ) {
            $this->result['receipt']= $receiptData['entity'][0];
        }
        
        $counters = $this->getCounter();
        $rangedOnDate= $this->getTimeRangeFixedOn($counters[0]->getOnDate());
        $newReceipt = new $receiptData['data_class']();
        
        $newReceipt->setDateB($rangedOnDate['begin'])
                ->setDateE($rangedOnDate['end'])
                ->setValue($counters[0]->getValue());
        
        $criteria = [
            'currentDate'=>$counters[0]->getOnDate(),
            'place'=>$counters[0]->getPlace()->getId(),
            'service'=>$counters[0]->getService()->getId()
        ];

        $tariff = $this->entityManager->getRepository(Tariff::class)->findActiveBy($criteria);
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
