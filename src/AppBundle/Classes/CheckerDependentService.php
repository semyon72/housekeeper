<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use AppBundle\Classes\Calculator\Calculators;


/**
 * Generated: Feb 12, 2019 12:20:46 AM
 * 
 * Description of CheckerDependentService
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
abstract class CheckerDependentService {
    
    protected $serviceGuidHandlers = [];
    
    
    public function __construct() {
        $this->initServiceGuidHandlers();
    }
    
    public function addGuidHandler($guid, callable $handler){
        $this->serviceGuidHandlers[$guid]= $handler;
        return $this;
    }
    
    public function removeGuidHandler($guid){
        if ( isset($this->serviceGuidHandlers[$guid]) ){
            unset($this->serviceGuidHandlers[$guid]);
        }
            
        return($this);
    }

    public function getGuidHandler($guid){
        $result= null;
        if ( isset($this->serviceGuidHandlers[$guid]) ){
            $result= $this->serviceGuidHandlers[$guid];
        }
            
        return($result);
    }

    
    protected function initServiceGuidHandlers(){
        $this->serviceGuidHandlers[]=[];
        //range - 9336c72c-ced9-4d15-8578-1947a6b28771
        //fixed - 20fc5e3a-51f6-493f-9c59-04a51ea735ef
        //cumulative - db5bf992-22ad-4917-bf13-71da1df21959
        foreach ( Calculators::getCalculators() as $class => $path) {
            $guid= $class::guid();
            $this->addGuidHandler($guid, $this->getDefaultGuidHandler($guid));
        }
                
    }

    /**
     * It must be redefined for other than standard handlers
     * 
     * @param string $guid
     * @return callable
     */
    protected function getDefaultGuidHandler($guid) {
        return [$this, '_'.str_replace('-', '_', $guid)];
    }
    
    abstract public function getGuid();

    
    public function Check() {
        $guid = $this->getGuid();
        return call_user_func_array($this->getGuidHandler($guid),[$this]);
    }
    
    
}
