<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use AppBundle\Form\FormTrait;

/**
 *
 * @author semyon
 */
trait FormTrait {
    //put your code here
    
    public function DEFAULT_OPTIONS_DATE_TIME_EDITABLE(){
        return ['date_widget'=>'single_text', 'with_seconds'=>true /*, 'date_format'=>'dd.MM.yyyy' */];
    }

    /**
     * 
     * @param string $fieldName
     * @param FormBuilderInterface $builder
     * @return FormBuilderInterface
     */
    public function getDefaultDateTimeField($fieldName, FormBuilderInterface $builder, array $options = array() ){
        $options = array_merge($this->DEFAULT_OPTIONS_DATE_TIME_EDITABLE(), $options);
        return $builder->create($fieldName, DateTimeType::class, $options);
    }


    
}
