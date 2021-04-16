<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Generated: Jan 30, 2019 7:02:53 PM
 * 
 * Description of ReceiptNewEditValidator
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class ReceiptNewEditValidator extends ConstraintValidator {
    //put your code here
    
    //  Example: How this custom validator can be attached to class/form-validation stream 
    //  
    //      
    //   class ReceiptType extends AbstractType
    //  { 
    //              
    //    /**
    //     * {@inheritdoc}
    //     */
    //    public function buildForm(FormBuilderInterface $builder, array $options)
    //    {         
    //     .........
    //     .........
    //    }         
    //              
    //              
    //    public function configureOptions(OptionsResolver $resolver)
    //    {
    //        $resolver->setDefaults(array(
    //            'data_class' => 'AppBundle\Entity\Receipt',
    //            'constraints' => [ new \AppBundle\Validator\ReceiptNewEdit() ]
    //        ));
    //    }
    //    
    //    ..........
    //    ..........
    //    ..........
    //    
    //  }
    
    
    
    
    /**
     * 
     * @param \Symfony\Component\Form $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint){
        
        $arguments = func_get_args();
        
        $receipt = $this->context->getValue();
//        $receipt->setValue(1111);
        
        $form = $this->context->getRoot();
//        $form->submit($receipt);
//        
//        $data = $form->getData();
        
        
        
        
//        $form->get('value')->setData(111);
        
        
//Example from ChoiceValidator        
//        if (null !== $constraint->min && $count < $constraint->min) {
//                $this->context->buildViolation($constraint->minMessage)
//                    ->setParameter('{{ limit }}', $constraint->min)
//                    ->setPlural((int) $constraint->min)
//                    ->setCode(Choice::TOO_FEW_ERROR)
//                    ->addViolation();
//
//                return;
//        }        
//Example from ChoiceValidator        
        
    }

}
