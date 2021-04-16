<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use AppBundle\Form\Settings\SettingsReceiptRounderType;
use AppBundle\Entity\Tariff;
use AppBundle\Entity\Place;
use AppBundle\Entity\Receipt;
use AppBundle\Entity\Scale;
use AppBundle\Classes\ReceiptRounderSessionStorage;

/**
 * Description of ReceiptRounder
 *
 * @author semyon
 */
class ReceiptRounder {
    //put your code here
    
    const DEFAULT_VALUE_SCALE = 3;
    
    const DEFAULT_TOTAL_SCALE = 2;

    /**
     *
     * @var EntityManagerInterface 
     */
    protected $entityManager = null;
    
    /**
     *
     * @var ReceiptRounderStorageInterface For example ReceiptRounderSessionStorage 
     */
    protected $storage = null;
    
    
    /**
     *
     * @var array Array of Scales
     */
    protected $settings = [];
    
    
    public function __construct( EntityManagerInterface $entityManager, ReceiptRounderStorageInterface $storage ) {
        $this->entityManager = $entityManager;
        $this->storage = $storage;
    }
    
    /**
     * Returns mix of placeId & serviceId for storing inside $this->settings
     * @param Scale $scale
     * @return string
     */
    protected function getScaleKey(int $placeId, int $serviceId ){
        return $placeId.'&'.$serviceId;
    }
    

    /**
     * 
     * @param Scale $scale
     * @return $this
     */
    protected function setDefaultScaleData(Scale $scale){
        $scale->setValueScale(self::DEFAULT_VALUE_SCALE)->setTotalScale(self::DEFAULT_TOTAL_SCALE);
        return $this;
    }
    
    /**
     * 
     * @param Tariff $tariff
     * @return $this
     */
    protected function initScaleFromTariff(Tariff $tariff){
        $place = $tariff->getPlace();
        $service = $tariff->getService();
        $scale = (new Scale())->setPlace($place)->setService($service);
        return $this->setDefaultScaleData($scale)->setScale($scale);
    }

    /**
     * 
     * @param int $placeId
     * @param int $serviceId
     * @return Scale|null
     */
    public function getScale(int $placeId, int $serviceId) {
        $result= null;
        $key = $this->getScaleKey($placeId, $serviceId);
      
        if ( isset($this->settings[$key]) ){
            $result = $this->settings[$key];
        }
        
        return $result;
    }


    /**
     * 
     * @param Tariff $tariff
     * @return Scale|null
     */
    public function getScaleForTariff(Tariff $tariff) {
        return $this->getScale($tariff->getPlace()->getId(), $tariff->getService()->getId());
    }

    
    /**
     * 
     * @param Scale $scale
     * @return $this
     */
    public function setScale(Scale $scale) {
        $key = $this->getScaleKey($scale->getPlace()->getId(), $scale->getService()->getId());
        $this->settings[$key]= $scale;
        
        return $this;
    }
    
    /**
     * Initialize default scale information from Tariff active rows. 
     * 
     * @return $this
     */
    public function InitDefaults(Place $place = null){
        $this->settings = [];
        $tariffRep = $this->entityManager->getRepository(Tariff::class);
        
        $criteria = [];
        if ( !is_null($place) ) {
            $criteria['place'] = $place;
        }
        //@todo Will need to get the unique values for 'place' + 'service' from 'tariff' table
        $tariffAll = $tariffRep->findActiveBy($criteria,['trf.place'=>'ASC', 'trf.service'=>'ASC']);
        foreach ($tariffAll as $tariff){
            $this->initScaleFromTariff($tariff['tariff']);
        }
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getScales(){
        return array_values($this->settings);
    }
    
    /**
     * 
     * @param array $scales
     * @return $this
     * @throws \Exception
     */
    public function setScales(array $scales){
        foreach ($scales as $scale){
            if ( is_a($scale, Scale::class) )  {
                $this->setScale($scale);
            } else {
                throw new \Exception('Row that indentified scale doesn\'t contains key \'tariffId\'.');
            }
        }
        return $this;
    }
    
    
    /**
     * Load settings of scale from storage.
     * 
     * @param Place $place
     * @return $this
     */
    public function load(Place $place = null) {
        $this->InitDefaults($place);
        $data= $this->storage->load($place);
        foreach ($data as $scale){
            $this->setScale($scale);
        }
        return $this;
    }
    
    /**
     * 
     * @return bool True if scale data exists. It doesn't mean that all scale data were loaded.
     */
    public function isLoaded(){
        return count($this->settings) > 0;
    }

    /**
     * 
     * @param array $data Collection of Scales
     * @return $this
     */
    public function store($data) {
        $this->storage->store($this->setScales($data)->getScales());
        return $this; 
    }
    
    /**
     * Apply scale from storage to passed $receipt.
     * 
     * @param Receipt $receipt
     * @return $this
     * @throws Exception
     */
    public function roundReceipt(Receipt $receipt){
        $scale= $this->getScaleForTariff($receipt->getTariff()); 
        if( !is_null($scale) ){
            $receipt->setValue( round( $receipt->getValue(), $scale->getValueScale()) );
            $receipt->setTotal( round( $receipt->getTotal(), $scale->getTotalScale()) );
        }
        return $this;
    }
    
}
