<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes\Calculator;

/**
 * Generated: Feb 1, 2019 6:47:08 AM
 * 
 * Description of CalculatorInterface
 * 
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
interface CalculatorInterface {
    //put your code here
    
    /**
     * Returns all errors after calculation
     * 
     * @return array List of error that appeared during the handling.
     */
    public function getErrors();
    
    
    /**
     * returns state of calculator, it means that no errors found during handling or it doesn't handled yet. 
     * 
     * @return boolean True is handled and valid, False otherwise.
     */
    public function isValid();
    
    /**
     * Returns human readable string that describe this calculator. 
     * 
     * @return string Description
     */
    static public function description();
    
    
    /**
     * Returns human readable string that describe this calculator. 
     * 
     * @return string Description
     */
    static public function guid();

    
    
    /**
     * Returns true if calculator was handled. 
     * 
     * @return boolean True if calculation was done at least once. False otherwise.
     */
    public function isHandled();
    
    
    /**
     * Handle calculator
     * 
     * @return mixed Returns result of handling
     */
    public function handle();
    
}
