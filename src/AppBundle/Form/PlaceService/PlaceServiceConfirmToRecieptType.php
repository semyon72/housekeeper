<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form\PlaceService;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;



/**
 * Generated: Feb 2, 2019 11:21:01 PM
 * 
 * Description of PlaceServiceConfirmToRecieptType
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class PlaceServiceConfirmToRecieptType extends AbstractType {
    
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {

        $builder->add('placeServeiceEntry', PlaceServiceMarkForReceiptEntryType::class)
                ->add('skip',SubmitType::class,['label'=>'Skip'])
                ->add('confirm',SubmitType::class,['label'=>'Confirm','attr'=>['value'=>'confirm']]);
        $builder->get('placeServeiceEntry')->setAttribute('label',false)->remove('mark')->add('mark', HiddenType::class);
        
    }
    
    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver) {
        $resolver->setDefault('label', false);
    }
    
    public function getBlockPrefix() {
        parent::getBlockPrefix();
    }
    
}
