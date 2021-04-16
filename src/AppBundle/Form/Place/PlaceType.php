<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


namespace AppBundle\Form\Place;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Place;

/**
 * Generated: Jan 21, 2019 10:03:29 PM
 * 
 * Description of PlaceForm
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class PlaceType  extends AbstractType {
    //put your code here
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        //parent::buildForm($builder, $options);
        $builder
            ->add('name',null,[ 'label'=>'Place description','attr'=>['class'=>'form-control', 'placeholder'=>'some name of place\'s description'] ])
            ->add('geo', null, ['label' => 'Geoposition data','attr'=>['class'=>"form-control", 'placeholder'=>'geoposition coordinates, like it\'s using by google maps']])
            ->add('save', SubmitType::class, ['label'=>'Save data', 'attr'=>['class'=>'btn-sm btn-primary']]);
    }
    
    
    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver) {
        //parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_place';
    }
    
    
}
