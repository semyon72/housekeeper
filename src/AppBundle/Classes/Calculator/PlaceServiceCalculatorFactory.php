<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes\Calculator;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Generated: Feb 4, 2019 12:12:17 PM
 * 
 * Description of PlaceServiceCalculatorFactory
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class PlaceServiceCalculatorFactory {
    
    static public function getCalculatorInstance( EntityManagerInterface $entityManager, $serviceId) {
        if ( !is_int($serviceId) ){
            throw new \Exception('Identifier of "service" must be integer type.');
        }
        
        $service = $entityManager->find('AppBundle\Entity\Service', $serviceId);
        if ( is_null($service) ){
            throw new Exception('Service with ID \''.$serviceId.'\' was not found.');
        }
        
        $calculatorClass = Calculators::getCalculatorClass($service->getCalculator());
        return ( new $calculatorClass($entityManager) );
    }
    
    
}
