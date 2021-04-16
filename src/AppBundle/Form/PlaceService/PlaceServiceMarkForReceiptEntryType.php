<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form\PlaceService;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


/**
 * Generated: Jan 27, 2019 6:48:37 PM
 * 
 * Description of PlaceServiceMarkForReceiptType
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class PlaceServiceMarkForReceiptEntryType extends AbstractType {
    //put your code here
    
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('placeService',HiddenType::class,['required'=>true, 'label'=>false])
                ->add('place',HiddenType::class,['required'=>true, 'label'=>false])
                ->add('service',HiddenType::class,['required'=>true, 'label'=>false])
                ->add('mark', CheckboxType::class, ['label'=>'Prepare for receipt', 'data'=> true,'required'=> false, 'label'=>false]);
                
    }
    
    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver) {
        $resolver->setDefault('label', false);
    }
    
    public function getBlockPrefix() {
        return 'appbundle_placeservicemarkforreceipt_entry';
    }
}
