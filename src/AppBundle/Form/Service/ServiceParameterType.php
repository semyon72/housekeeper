<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form\Service;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints;

use AppBundle\Entity\ServiceParameter;
use AppBundle\Entity\Service;
use AppBundle\Classes\Transformers\EntityIdTransformer;

/**
 * Generated: Feb 14, 2019 11:48:21 PM
 * 
 * Description of ServiceParameterType
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class ServiceParameterType extends AbstractType {
    //put your code here
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder->add('parameter', null, ['label'=>'Parameter of service'])
                ->add('service', Type\HiddenType::class, [
                    'label'=>'Service name',
                    'constraints'=>[
                        new Constraints\NotNull()
                    ]
                ])
                ->add('save', Type\SubmitType::class,['label'=>'Save data']);
        
        $builder->get('service')->addModelTransformer( new EntityIdTransformer($options['entity_manager'], Service::class) );
        
    }
    
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefault('data_class', ServiceParameter::class )
                ->setRequired('entity_manager');
    }
    
    public function getBlockPrefix() {
        return 'appbundle_'.parent::getBlockPrefix();
    }
}
