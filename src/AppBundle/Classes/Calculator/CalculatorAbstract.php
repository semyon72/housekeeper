<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes\Calculator;


use Doctrine\ORM\EntityManagerInterface;

/**
 * Generated: Feb 1, 2019 7:03:31 AM
 * 
 * Description of CalculatorAbstract
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
abstract class CalculatorAbstract implements CalculatorInterface {
    
    /**
     *
     * @var \Doctrine\ORM\EntityManagerInterface 
     */
    protected $entityManager = null;
    
    /**
     *
     * @var array 
     */
    protected $errors = [];
    
    
    /**
     * Sign of It is was handled
     * 
     * @var boolean
     */        
    protected $isHandled = false;
    
    /**
     * Contains result of last handling
     * 
     * @var mixed
     */
    protected $result = null;
    
    
    public function __construct(EntityManagerInterface $entitytManager) {
        $this->entityManager = $entitytManager;
    }
    
    /**
     * 
     * {@inheritdoc}
     * 
     * @return string
     */
    static public function description() {
        return 'You need redefine this method "'.__METHOD__.'" for get appropriate message.';
    }

    /**
     * 
     * @return string
     */
    static public function guid(){
        return 'fdc37aec-e00b-4416-b088-664fb5d82e53 You need redefine in descendants'; //https://www.guidgenerator.com/
    }    
    
    /**
     * 
     * {@inheritdoc}
     * 
     */
    public function getErrors() {
        return $this->errors;
    }
    
    protected function clearError(){
        $this->errors = [];
        return $this;
    }
    
    protected function addErorr($error, $errorName = '') {
        if (empty($errorName)) {
            $this->errors[]= $error;
        } else {
            $this->errors["$errorName"]= $error;
        }
        return $this;
    }
    
    
    public function isHandled() {
        return $this->isHandled === true; //type checking
    }
    

    public function isValid() {
        return count($this->getErrors()) === 0  ;
    }

    /**
     * Returns result of handling
     * 
     * @return mixed Result depends from descendant that operate appropriate type or null otherwise.
     */
    public function getResult() {
        
        return $this->isHandled() ? $this->result : null;
    }
    
    
    abstract public function handle();

    
}
