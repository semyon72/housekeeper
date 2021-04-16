<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes;

use Symfony\Component\Form\FormEvent;

use AppBundle\Entity\Tariff;


/**
 * Generated: Jan 31, 2019 12:17:41 AM
 * 
 * Description of CheckNewEditReceipt
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class CheckNewEditReceipt {
    //put your code here
    
    /**
     *
     * @var FormEvent 
     */
    private $event;
    
    public function __construct(FormEvent $event) {
        $this->event = $event;
    }
    
    /**
     * Will use in ReceiptType
     * 
     *  $builder->addEventListener(Form\FormEvents::PRE_SUBMIT, function (Form\FormEvent $event) {
     *      $data= $event->getData();
     *
     *      $data['value']= 2222;
     *       
     *      $event->setData($data);
     *   });
     * 
     * @param array $receipt
     */
    public function preCheck(){
        $data = $this->event->getData();
        
        if ( isset($data['calculate']) && $data['calculate'] == true  ){

            $form = $this->event->getForm();
            $em = $form->getConfig()->getOption('entity_manager');
            $tariff = $em->find(Tariff::class, $data['tariff']);
            
            $value = (int)$data['valueE'] - (int)$data['valueB'];
            if ( $value > 0 ) $data['value'] = $value;
            else { 
                $data['valueE'] = 0;
                $data['valueB'] = 0;
            }
            
            $data['total'] = round($data['value'] * $tariff->getUnitValue(),4);
        }
        
        $this->event->setData($data);
    }
    
    
    
    
}
