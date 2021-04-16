<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes\Calculator;

use Symfony\Component\ClassLoader\ClassMapGenerator;
/**
 * Generated: Feb 2, 2019 12:33:37 AM
 * 
 * Description of Calculators
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
final class Calculators {
    
    static private $mySelf = null;
    
    protected $calculators = [];
    
    protected $guidedCalculators = [];
    
    
    private function __construct() {
        $this->makeCalculatorsMap();
    }
    /**
     * 
     * @return Calculators Singleton 
     */
    static public function instance(){
        if ( is_null(self::$mySelf) ) self::$mySelf = new self();
        
        return(self::$mySelf);
    }
    
    public function makeCalculatorsMap($namespace = '\\AppBundle\\Classes\\Calculator\\'){
        $this->calculators = [];
        $this->guidedCalculators = [];
        
        $tCalculators = ClassMapGenerator::createMap(__DIR__);
        foreach ($tCalculators as $className=>$filePath){
            $refClass = new \ReflectionClass($className);
            $ifName = rtrim($namespace,'\\').'\\CalculatorInterface';
            if ( $refClass->implementsInterface($ifName) &&
                 $refClass->isSubclassOf($ifName) &&
                 !$refClass->isAbstract() ){
                $this->calculators[$className]= $filePath;
                $this->guidedCalculators[$className::guid()]= $className; 
            }
        } 
    }
    
    static public function getCalculators() {
        return self::instance()->calculators;
    }

    static public function getGuidedCalculators() {
        return self::instance()->guidedCalculators;
    }
    
    static public function getCollectionCalculators(){
        $result = [];
        foreach ( self::getCalculators() as $className=>$fileName ){
            $result[$className::description()]= $className::guid();
        }
        return $result;
    }
    
    static public function getCalculatorClass($guid){
        if ( isset(self::instance()->guidedCalculators[$guid]) ) {
            return self::instance()->guidedCalculators[$guid];
        }
        return null;
    }
    
}
