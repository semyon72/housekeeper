<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes\ControllerTrait;

use AppBundle\Classes\Calculator\CumulativeCalculator;

/**
 * Generated: Feb 24, 2019 12:16:10 AM
 * 
 * Description of PlaceServiceControllerTrait
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
trait PlaceServiceControllerTrait {
    
    
    private function getPlaceServiceMarkForReceiptCollection(array $placeServices){
        $result = array();
        foreach ($placeServices as $placeService){
            $entry = new \stdClass();
            $entry->placeService= $placeService->getId();
            $entry->place= $placeService->getPlace()->getId();
            $entry->service= $placeService->getService()->getId();
            $entry->mark= false;
            $result['placeServiceMarkForReceipt'][] = (array)$entry;
        }
        
        return( $result );
    }
    
    /**
     * 
     * @param array $placeServices
     * @return array of \AppBundle\Entity\PlaceService
     */
    private function pushCumulativeCalculatorsToEnd(array $placeServices){
        $result= [];
        $cumulative = [];
        foreach ( $placeServices as $idx=>$placeService){
            $service = $placeService->getService();
            if ( $service->getCalculator() === CumulativeCalculator::guid() ){
                $cumulative[] = $placeService;
            } else {
                $result[]= $placeService;
            }
        }
        
        return array_merge($result,$cumulative);
    }
    
    
    
    
    
    
    

}
