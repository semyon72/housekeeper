<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes\ControllerTrait;

use AppBundle\Entity;
use AppBundle\Classes\Calculator\CumulativeCalculator;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 *
 * @author Semyon Mamonov
 */
trait ServiceParameterControllerTrait {
    
    private function isParameterizedService(Entity\Service $service){
        return $service->getCalculator() === 'db5bf992-22ad-4917-bf13-71da1df21959'; // Cumulative calculator 
    }
    
    private function getServicesListExcludeCurrent(Entity\Service $currentService){
        $result = [];
        $em= $this->getDoctrine()->getManager();
        
        $services= $em->getRepository(Entity\Service::class)->findAll();
        foreach ( $services as $key=>$service ){
            if ($service->getId() !== $currentService->getId()){
                $result[strval($service->getId())]=$service->getName(); //bug (glitch) with comparison value like '1kjhkjhk' with key 1 will have true result; 
            }
        }
        
        return $result;
    }
    
    /**
     * 
     * @param Form $form
     * @param \AppBundle\Entity\Service $currentService
     * @return Form
     */
    private function tweakParameter(Form $form, Entity\Service $currentService, $currentParameter = null){
        
       if ( $currentService->getCalculator() === CumulativeCalculator::guid() ){
            $parameterForm = $form->get('parameter');
            $form->remove('parameter');
            $serviceList = $this->getServicesListExcludeCurrent($currentService);
            
            $em= $this->getDoctrine()->getManager();
            $parameters= $em->getRepository(Entity\ServiceParameter::class)->findBy(['service'=>$currentService->getId()]);
            foreach($parameters as $idx=>$parameter){
                $serviceId = $parameter->getParameter();
                if ( isset($serviceList[$serviceId]) && ( is_null($currentParameter) || $currentParameter->getParameter() !== $serviceId)  ){
                    unset($serviceList[$serviceId]);
                }
            }
            
            $form->add('parameter', ChoiceType::class, [
                'label'=> $parameterForm->getConfig()->getOption('label'),
                'choices' => array_flip($serviceList),
            ]);
       }
       
       return $form;
    }
    
    
}
