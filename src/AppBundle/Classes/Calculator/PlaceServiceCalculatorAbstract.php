<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes\Calculator;

/**
 * Generated: Feb 1, 2019 7:46:46 AM
 * 
 * Description of PlaceServiceCalculatorAbstract
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
abstract class PlaceServiceCalculatorAbstract extends CalculatorAbstract {
    
    protected $data = [];
    
    public function __construct(\Doctrine\ORM\EntityManagerInterface $entitytManager) {
        parent::__construct($entitytManager);
        $this->emptyDefaultData();
    }
    
    private function emptyDefaultData(){
        $this->data = [
            'place_service' =>[
                'data_class'=>'AppBundle\Entity\PlaceService',
                'place'=>0,
                'service'=>0,
                'entity'=>null
            ],
            'receipt' =>[
                'data_class'=>'AppBundle\Entity\Receipt',
                'entity'=>null
            ],
            'counter' =>[
                'data_class'=>'AppBundle\Entity\Counter',
                'entity'=>null
            ]
        ];
        return($this);
    }
    
    protected function clearData(){
        foreach ($this->data as $dataType=>&$params){
            $params['entity'] = null;
        }
        return $this;
    }
    
    protected function clear(){
        $this->result = null;
        $this->isHandled = null;
        $this->clearError();
        $this->clearData();
        return $this;
    }
    
    
    /**
     * 
     * @return array contains [ 'data_class'=>'AppBundle\Entity\PlaceService', 'place'=>0, 'service'=>0, 'entity'=>null ]
     */
    public function get($name) {
        if ( isset($this->data[$name])  ) return $this->data[$name];
        else throw new \Exception('Internal data doesn\'t contain information with "'."$name". '" key.');
    }

    
    /**
     * Set identifiers like a input data for handling
     * 
     * @param integer $placeId IDentifier of 'place' in 'place' table of schema. 
     * @param integer $serviceId IDentifier of 'service' in 'service' table of schema. 
     * @throws Exception Throw exceptions if $placeId, $serviceId are nod integers.
     * @return PlaceServiceCalculatorAbstract Returns itself
     */
    public function setPlaceService($placeId, $serviceId) {
        if ( !is_int($placeId) || !is_int($serviceId) ){
            throw new \Exception('Identifiers "place" and "service" must be integer type.');
        }
        
        $this->data['place_service']['place'] = $placeId;
        $this->data['place_service']['service'] = $serviceId;
        
        return $this;
    }

    public function getReceipt() {
        return $this->get('receipt')['entity'];
        
    }
    
    public function getCounter() {
        return $this->get('counter')['entity'];
    }
    
    public function getPlaceService() {
        return $this->get('place_service')['entity'];
    }

    /**
     * Returns empty object that created without invoking constructor.
     * 
     * @param string $name It is one key from keys pointed in $this->data[...] the 'place_service' or 'service' or 'counter' for example.
     * @return object
     */
    protected function getEmptyObject($name){
        $result= null;
        if ( isset($this->data[$name]) ){
            $result = ( new \ReflectionClass($this->data['receipt']['data_class']) )->newInstanceWithoutConstructor();
        }
        return $result;
    }    
    
    /**
     * By default logic must set $this->data['receipt']['entity'] as array of receipt entities.
     * It was done for create common rules store entities, exclude $this->data['place_service'].
     * And for receipt it will (must) contains exact  one row (by default). But not limited if need.
     * 
     * @return $this
     */
    protected function initializeReceipt(){
        $placeService = $this->get('place_service')['entity'];
        $criteria = ['place'=>$placeService->getPlace()->getId(), 'service'=>$placeService->getService()->getId()];
        $qb = $this->entityManager->createQuery('SELECT r, t FROM '. $this->get('receipt')['data_class'].' r JOIN r.tariff t WHERE t.place = :place AND t.service = :service ORDER BY r.dateE DESC');
        $this->data['receipt']['entity'] = $qb->setMaxResults(1)->execute($criteria);
        return $this;
    }


    /**
     * By default logic must set $this->data['counter']['entity'] as array of counter entities 
     * It was done for create common rules store entities, exclude $this->data['place_service'].
     * And for counter it will contains <= 3 rows (by default). But not limited if need.
     * 
     * @return $this
     */
    protected function initializeCounter(){
        $placeService = $this->get('place_service')['entity'];
        $criteria = ['place'=>$placeService->getPlace()->getId(), 'service'=>$placeService->getService()->getId()];
        $this->data['counter']['entity'] = $this->entityManager->getRepository($this->get('counter')['data_class'])->findBy($criteria,['onDate'=>'DESC'],3);

        return $this;
    }

    
    protected function initialize(){
        $this->clear();
        
        $placeService = $this->get('place_service');
        $criteria = ['place'=>$placeService['place'],'service'=>$placeService['service']];
        $this->data['place_service']['entity']= $this->entityManager->getRepository($placeService['data_class'])->findOneBy($criteria);
        
        $placeService = $this->get('place_service')['entity'];
        if ( is_null($placeService ) ) {
            throw new \Exception('Something went wrong. Appropriate PlaceService was not found.');
        }

        $this->initializeReceipt();
        $this->initializeCounter();
        
        return $this;
    }

    /**
     * Make validation (to set Errors) and do something before will be invoked
     * prepareResult(). For chaining can returns $this but not mandatory.
     * In current handle() implementation the value that returns is not using at all.    
     */
    abstract protected function validate();
    
    /**
     * Need implements some logic for collection resulted data and to set
     * $this->result['receipt'] and $this->result['receipt_nes'] in appropriate
     * objects for getting these values as result of invoke the getResult()
     * method that the belongs to CalculatorAbstract and used by standart
     * handle() implementation for return the resulted values.    
     */
    abstract protected function prepareResult();
    
    
    public function handle() {
        $this->initialize()->validate();
        $this->isHandled=true;
        if ( $this->isValid() ) {
            $this->prepareResult();
        }
        return $this->getResult();
    }
    
    
}
