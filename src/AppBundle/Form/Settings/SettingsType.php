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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use AppBundle\Entity\Place;

/**
 * Generated: Feb 13, 2019 3:26:04 PM
 * 
 * Description of SettingsType
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class SettingsType extends AbstractType{
    //put your code here
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
       $builder->add('place', EntityType::class, [
                'label'=>'Select default place',
                'required'=>false,
                'class'=> Place::class,
                'choice_label'=>'name',
           ])
            ->add('submit', SubmitType::class, ['label'=>'Submit settings']);
    }
    
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        //$resolver->setDefault('data_class', \stdClass::class);
    }
    
    public function getBlockPrefix() {
        return('appbundle_'.parent::getBlockPrefix());
    }
    
}
