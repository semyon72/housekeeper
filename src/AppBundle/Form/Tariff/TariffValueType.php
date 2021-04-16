<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form\Tariff;

use Symfony\Component\Form\AbstractType;
use AppBundle\Entity\TariffValue;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use AppBundle\Classes\Transformers\EntityIdTransformer;
use AppBundle\Entity\Tariff;

/**
 * Generated: Feb 20, 2019 9:55:56 PM
 * 
 * Description of TariffValueType
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class TariffValueType extends AbstractType {
    
    
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        
        $builder->add('valueFrom', null, ['label'=>'Begin of value range',
                'scale'=>4,
                'required'=>true,
                'constraints'=>[ new NotNull(), new Type('numeric') ]
            ])
            ->add('valueTo', null, ['label'=>'End of value range',
                'scale'=>4,
                'required'=>false,
                'constraints'=>[
                    new \Symfony\Component\Validator\Constraints\Callback( function($valueTo, ExecutionContextInterface $context, $payload){
                        if ( !is_null($valueTo) && !is_numeric($valueTo) ) {
                            $context->buildViolation('This value must be null or numeric. Null means that up limit of range is no limited.')
                                ->atPath('valueTo')
                                ->addViolation();
                        }
                    })
                ]
            ])
            ->add('unitValue', null, ['label'=>'Tariff per unit',
                'required'=>true,
                'constraints'=>[ new NotNull(), new Type('numeric') ]
            ])
            ->add('tariff', HiddenType::class, ['label'=>'Tariff per unit',
                'required'=>true,
                'constraints'=>[ new NotNull(), new Type('object') ]
            ])
            ->add('save', SubmitType::class,['label'=>'Save data']);

        $builder->get('tariff')->addModelTransformer( new EntityIdTransformer($options['entity_manager'], Tariff::class));

    }
    
    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('data_class', TariffValue::class );
        $resolver->setRequired('entity_manager');
    }
    
    public function getBlockPrefix() {
        return 'appbundle_'.parent::getBlockPrefix();
    }
    
    
}
