<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Classes\ControllerTrait;

use Symfony\Component\Form;
use AppBundle\Form\Settings\SettingsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormBuilder; //Form->getConfig()
use Symfony\Component\Form\FormRegistry; //Symfony\Component\Form\FormRegistryInterface | 'form.registry'
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Form\FormView;
  

/**
 * Generated: Feb 13, 2019 6:41:45 PM
 * 
 * Description of SettingsControllerTrait
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
trait SettingsControllerTrait {

    protected function checkCompatibles(){
        if ( !is_subclass_of($this, \Symfony\Bundle\FrameworkBundle\Controller\Controller::class)  ){
            throw new \Exception('Trait works only with [Symfony\Bundle\FrameworkBundle\Controller\Controller] classes. But was applied to ['.get_called_class().']');
        }
    }


    /**
     * 
     * @param Form\Form $form
     * @return Form\Form that was passed into function as parameter
     * @throws \Exception
     */
    protected function setDefaultPlace(Form\Form $form){
        
        $this->checkCompatibles();
        
        $formPlace = null;
        if ( $form->has('place') ){
            $formPlace = $form->get('place');
            
            $settingsForm = $this->createForm(SettingsType::class);
            $session = $this->get('session');
            if ( $session->has($settingsForm->getName()) ){
                $sessSettingsData = $session->get($settingsForm->getName());
                if ( isset($sessSettingsData['place']) ){
                    $em= $this->getDoctrine()->getManager();
                    if ( !$em->contains($sessSettingsData['place'])) {
                        $sessSettingsData['place'] = $em->find(get_class($sessSettingsData['place']),$sessSettingsData['place']->getId());
                    }
                }
                $formPlace->setData($sessSettingsData['place']);
            }
        }
        return $this;
    }
    
    /**
     * 
     * @param \Symfony\Component\Form\Form $filterForm
     * @param Request $request
     * @param string $submitName 
     * @return Form\Form
     */
    protected function setFilterAndHandleRequest(Form\Form $filterForm, Request $request = null){
        $this->checkCompatibles();

        if( is_null($request) ){
            $request = $this->get('request_stack')->getCurrentRequest();
        }
        
        $sessionName = $filterForm->getName();
        $session = $this->get('session');
        $filterForm->handleRequest($request);
        if( $filterForm->isSubmitted() ){
            if ( $filterForm->isValid() ){
                $formOptions= $filterForm->getConfig()->getOptions();
                $formView = $filterForm->createView();
                $sesData = [];
                foreach ($formView as $elemKey => $element ){
                    if (!$formOptions['csrf_protection'] || $elemKey !== $formOptions['csrf_field_name'] ){
                        $sesData[$elemKey] = $element->vars['value'];
                    }
                }
                
                $session->set($sessionName, $sesData);
            }
        } else {
            if ( $session->has($sessionName) ){
                $filterForm = $this->createForm(get_class($filterForm->getConfig()->getType()->getInnerType()),$filterForm->getData());
                $sesData = $session->get($sessionName);
                $formOptions= $filterForm->getConfig()->getOptions();
                if ( $formOptions['csrf_protection'] ){
                    $token = $formOptions['csrf_token_manager']->getToken($filterForm->getName());
                    $sesData[$formOptions['csrf_field_name']]= $token->getValue();
                }
                
                $filterForm->submit($sesData);
            }
        }
        
        return $filterForm;
    }


    
}
