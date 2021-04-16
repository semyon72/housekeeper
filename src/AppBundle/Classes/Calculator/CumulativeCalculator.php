<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes\Calculator;

use AppBundle\Classes\Calculator\PlaceServiceCalculatorAbstract;
use AppBundle\Entity\ServiceParameter;
use AppBundle\Entity\Service;

/**
 * Generated: Feb 23, 2019 10:49:45 PM
 * 
 * Description of CumulativeCalculator
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class CumulativeCalculator extends PlaceServiceCalculatorAbstract {
    
    const START_RECEIPT_NOT_FOUND_ERROR = 'Start record wasn\'t found in "Receipts" for this cumulative/virtual service.';
    const SERVICES_ON_WHICH_CURRENT_ONE_DEPENDS_MUST_EXIST_ERROR = 'SERVICES_ON_WHICH_CURRENT_ONE_DEPENDS_MUST_EXIST_ERROR';
    const RECEIPTS_ON_WHICH_CURRENT_ONE_DEPENDS_MUST_EXIST_ERROR = 'RECEIPTS_ON_WHICH_CURRENT_ONE_DEPENDS_MUST_EXIST_ERROR';
    const PROPOSED_RECEIPT_VALUE_BEGIN_LESS_THAN_START_RECEIPT_ERROR = 'PROPOSED_RECEIPT_VALUE_LESS_THAN_START_RECEIPT_ERROR';
    const PROPOSED_RECEIPT_VALUE_END_LESS_THAN_START_RECEIPT_ERROR = 'PROPOSED_RECEIPT_VALUE_LESS_THAN_START_RECEIPT_ERROR';
    
    /**
     * Key contains guid and value is full class qualifier.
     *  
     * @var array List of guids of services from which dependent this calculator. 
     */
    protected $services = [];
    
    /**
     * 
     * @param mixed $service Service ID or instance of AppBundle\Entity\Service class
     * @return $this
     * @throws Exception
     */
    public function addService($service){
        if ( is_int($service) || is_numeric($service)){
            $service = $this->entityManager->find(Service::class, $service);
        }
        
        if (!is_null($service) && is_a($service, Service::class)) {
            $this->services[$service->getId()]= $service;
        } else {
            throw new Exception('Service with id - \''.$service.'\' not found.');
        }
        return $this;
    }
    
    /**
     * 
     * @param mixed $service Service ID or instance of AppBundle\Entity\Service class
     * @return $this
     */
    public function removeService($service){
        if (!is_null($service) && is_a($service, Service::class)){
            $serviceId = $service->getId();
        } else {
            $serviceId = intval($service);
        }
        
        $isExists = array_key_exists($serviceId, $this->services);
        if ( $isExists !== false ){
            unset($this->services[$serviceId]);
            $this->services = array_filter($this->services);
        }
        return $this;
    }
    
    public function clearServices(){
        $this->services = [];
        return $this;
    }

    
    static public function guid() {
        return 'db5bf992-22ad-4917-bf13-71da1df21959'; //https://www.guidgen.com/
    }
    
    static public function description() {
        return '* Cumulative - Depends from few other. *';
    }
    
    /**
     * 
     * Alias to $this->getCounter() because cumulative calculation don't need counters
     * at all. But we need place for store the dependent receipts.
     * 
     * @return array Array of dependent receipts.
     */
    public function getDependentReceipts(){
        return $this->getCounter();
    }
    
    protected function initializeCounter() {
        //In this place we will prepare a criterias for initialization the counters by dependent receipts.
        //At this moment the last receipt has been received.
        $receipts = $this->getReceipt();
        $criteria = ['place'=>$this->getPlaceService()->getPlace()->getId(), 'dateE'=> null, 'service'=>null ];
        if ( is_array($receipts ) && count($receipts ) === 1){
            $criteria['dateE']= $receipts[0]->getDateE();
        }
        
        //will be used not as intended.
        //We dont need counters at all but we must get id's of services from that dependens our service.
        $service = $this->getPlaceService()->getService();
        $parameters = $this->entityManager->getRepository(ServiceParameter::class)->findBy(['service'=>$service->getId()]);
        foreach ($parameters as $idx=>$parameter){
            $this->addService($parameter->getParameter());
            
            if ( !is_null($criteria['dateE']) ){
                $criteria['service']= $parameter->getParameter();
                $qb = $this->entityManager->createQuery('SELECT r, t FROM '. get_class($receipts[0]).' r JOIN r.tariff t WHERE t.place = :place AND t.service = :service AND r.dateE > :dateE ORDER BY r.dateE ASC');
                $depReceipt = $qb->setMaxResults(1)->execute($criteria);
                if ( count($depReceipt) > 0 ) {
                    $this->data['counter']['entity'] = array_merge( (array) $this->data['counter']['entity'], $depReceipt );
                }
            }
            
        }
        
        return $this;
    }

    
    protected function validate() {
        //need check if services is empty
        if (count($this->services) == 0) {
            $this->addErorr(self::SERVICES_ON_WHICH_CURRENT_ONE_DEPENDS_MUST_EXIST_ERROR, 'SERVICES_ON_WHICH_CURRENT_ONE_DEPENDS_MUST_EXIST_ERROR');
            return false;
        }
        
        $receipts = $this->getReceipt();
        if ( !is_array($receipts ) || count($receipts ) === 0){
            $this->addErorr(self::START_RECEIPT_NOT_FOUND_ERROR,'START_RECEIPT_NOT_FOUND_ERROR');
            return false;
        } 
        
        $depReceipts= $this->getDependentReceipts();
        if ( !is_array($depReceipts ) || count($depReceipts ) !== count($this->services) ){
            $this->addErorr(self::RECEIPTS_ON_WHICH_CURRENT_ONE_DEPENDS_MUST_EXIST_ERROR,'RECEIPTS_ON_WHICH_CURRENT_ONE_DEPENDS_MUST_EXIST_ERROR');
            return false;
        }
        
        return $this;
    }

    
    protected function prepareResult() {
        
        $dependentReceipts = $this->getDependentReceipts();

        $receiptData = $this->get('receipt');
        $prevReceipt= $receiptData['entity'][0];
        $this->result['receipt'] = $prevReceipt;
        
        $newReceipt = new $receiptData['data_class']();
        for ($i = 0; $i < count($dependentReceipts); $i++){
            $receipt = $dependentReceipts[$i];

            if ( $i === 0){ 
                $newReceipt->setValueE($receipt->getValueE())
                        ->setValueB($receipt->getValueB())
                        ->setValue($receipt->getValue());
                $newReceipt->getDateB()->setTimestamp($receipt->getDateB()->getTimestamp());
                $newReceipt->getDateE()->setTimestamp($receipt->getDateE()->getTimestamp());
                continue;
            }
            
            $newReceipt->setValueB($newReceipt->getValueB() + $receipt->getValueB());
            $newReceipt->setValueE($newReceipt->getValueE() + $receipt->getValueE());
            $newReceipt->setValue($newReceipt->getValue() + $receipt->getValue());
            if ( $receipt->getDateB() < $newReceipt->getDateB() ) {
                $newReceipt->getDateB()->setTimestamp($receipt->getDateB()->getTimestamp());
            }
            if ( $receipt->getDateE() > $newReceipt->getDateE() ) {
                $newReceipt->getDateE()->setTimestamp($receipt->getDateE()->getTimestamp());
            }
        }
        
        if ( $prevReceipt->getDateE() >= $newReceipt->getDateE() ) {
            $newReceipt->getDateE()->setTimestamp($prevReceipt->getDateE()->getTimestamp())->modify('+1 sec');
        }

        if ( $newReceipt->getValueB() < $prevReceipt->getValueB() ){
            $this->addErorr(self::PROPOSED_RECEIPT_VALUE_BEGIN_LESS_THAN_START_RECEIPT_ERROR,'PROPOSED_RECEIPT_VALUE_BEGIN_LESS_THAN_START_RECEIPT_ERROR');
        } else if ( $newReceipt->getValueE() < $prevReceipt->getValueE() ){
            $this->addErorr(self::PROPOSED_RECEIPT_VALUE_END_LESS_THAN_START_RECEIPT_ERROR,'PROPOSED_RECEIPT_VALUE_END_LESS_THAN_START_RECEIPT_ERROR');
        }
        
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
