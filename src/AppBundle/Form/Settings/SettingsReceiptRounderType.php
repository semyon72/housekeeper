<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form\Settings;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

/**
 * Generated: Feb 13, 2019 3:26:04 PM
 * 
 * Description of SettingsType
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class SettingsReceiptRounderType extends AbstractType{
    //put your code here
    
    const SETTINGS_RECEIPT_ROUNDER_COLECTION_FORM_NAME = 'settingsReceiptRounderEntry';

    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
       $builder->add(self::SETTINGS_RECEIPT_ROUNDER_COLECTION_FORM_NAME, Type\CollectionType::class, [
                'required'=>true,
                'label'=>false,
                'entry_type'=> SettingsReceiptRounderEntryType::class,
                'entry_options'=>['entity_manager'=>$options['entity_manager']]
               ])
            ->add('submit', Type\SubmitType::class, ['label'=>'Submit scales']);
              
    }
    
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        //$resolver->setDefault('data_class', \stdClass::class);
        $resolver->setDefault('show_legend', false);
        $resolver->setDefault('entity_manager', null);
    }
    
    public function getBlockPrefix() {
        return('appbundle_'.parent::getBlockPrefix());
    }
    
}
