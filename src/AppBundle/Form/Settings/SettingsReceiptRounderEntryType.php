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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use AppBundle\Classes\Transformers\EntityIdTransformer;
use AppBundle\Entity\Scale;
use AppBundle\Entity\Place;
use AppBundle\Entity\Service;


/**
 * Generated: Feb 13, 2019 3:26:04 PM
 * 
 * Description of SettingsType
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class SettingsReceiptRounderEntryType extends AbstractType{
    //put your code here
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        
        $builder->add('place', HiddenType::class, [
                'label'=>false,
                'required'=> true,
           ])
                ->add('service', HiddenType::class, [
                'label'=>false,
                'required'=> true,
           ])
                ->add('valueScale', IntegerType::class, [
                'label'=>'Scale for any Values',
                'required'=>true
           ])
                ->add('totalScale', IntegerType::class, [
                'label'=>'Scale for Total',
                'required'=>true
           ]);
        
        $builder->get('place')->addModelTransformer( new EntityIdTransformer($options['entity_manager'], Place::class) );
        $builder->get('service')->addModelTransformer( new EntityIdTransformer($options['entity_manager'], Service::class) );
    }
    
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        $resolver->setDefault('data_class', Scale::class);
        $resolver->setDefault('entity_manager', null);
    }
    
    public function getBlockPrefix() {
        return('appbundle_'.parent::getBlockPrefix());
    }
    
}
