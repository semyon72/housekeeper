<?php

/*
 * The MIT License
 *
 * Copyright 2020 Semyon Mamonov <semyon.mamonov@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace AppBundle\Form\Utils;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Classes\MiscellaneousUtils;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Description of IBANCalculatorType
 *
 * Generated: Jan 27, 2020 11:09:46 PM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class IBANCalculatorType extends AbstractType {
    //put your code here
    
    
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        
        $builder->add('countryCode', ChoiceType::class,[
                'label'=>'Country code',
                'choices'=> array_flip(MiscellaneousUtils::COUNTRY_CODES_2)
            ])->add('bankCode',TextType::class,[
                'label'=>'Bank code',
                'constraints'=>[
                    new Type([
                        'type'=>'alnum'
                    ]),
                    new Callback([$this,'testLengthBankCode'])
                ]
            ])->add('bankCodeLength', IntegerType::class,[
                'attr'=>['min'=>1,'max'=>30, 'maxlength'=>2, 'size'=>2],
                'label'=>'Length of "Bank code"',
                'data'=>6
            ])->add('accountNumber',TextType::class,[
                'label'=>'Account number',
                'constraints'=>[
                    new Type([
                        'type'=>'alnum'
                    ]),
                    new Callback([$this,'testLengthAccountNumber'])
                ]
            ])->add('accountNumberLength', IntegerType::class,[
                'attr'=>['min'=>1,'max'=>30, 'maxlength'=>2, 'size'=>2],
                'label'=>'Length of "Account number"',
                'data'=>19
            ]);
        
    }
    
    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver) {
        parent::configureOptions($resolver);
    }
    
    public function getBlockPrefix() {
        return parent::getBlockPrefix();
    }
    
    protected function testLength($fieldNameLength, $errorMessage,  $object, ExecutionContextInterface $context, $payload){
        $current= $context->getObject();
        $length = $context->getRoot()->get($fieldNameLength)->getData();
        if ( strlen($object) > (int)$length){
            $context->buildViolation($errorMessage)
                    ->atPath($current->getName())
                    ->addViolation();
        }
        return $this;
    }
    
    public function testLengthBankCode($object, ExecutionContextInterface $context, $payload){
        $this->testLength('bankCodeLength', 'Length of string in "Bank code" field more than number in the field "Length of "Bank code".', $object, $context, $payload);
        return $this;
    }
    
    public function testLengthAccountNumber($object, ExecutionContextInterface $context, $payload){
        $this->testLength('accountNumberLength', 'Length of string in "Account number" field more than number in the field "Length of "Account number".', $object, $context, $payload);
        return $this;
    }
    
}
