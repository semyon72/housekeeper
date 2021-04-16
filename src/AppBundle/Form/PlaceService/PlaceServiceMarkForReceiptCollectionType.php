<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form\PlaceService;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


/**
 * Generated: Jan 27, 2019 6:48:37 PM
 * 
 * Description of PlaceServiceMarkForReceiptType
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class PlaceServiceMarkForReceiptCollectionType extends AbstractType {
    //put your code here
    
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('placeServiceMarkForReceipt', CollectionType::class,
                        ['entry_type' => PlaceServiceMarkForReceiptEntryType::class]
                    )
                ->add('prepare', SubmitType::class, ['label'=>'Prepare for receipt']);
                
    }
    
    public function getBlockPrefix() {
        return 'appbundle_placeservicemarkforreceipt';
    }
}
