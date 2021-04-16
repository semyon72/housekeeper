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

namespace AppBundle\Form\Login;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Description of ConfirmEmailType
 *
 * Generated: Jan 12, 2020 4:05:15 PM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class ConfirmEmailType extends AbstractType {
    //put your code here
    
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        $otpPattern = '/^[0-9a-f]{64}$/';
        if (isset($options['otp_validation_pattern_length'])){
            $otpPattern = str_replace('{64}', '\''.$options['otp_validation_pattern_length'].'\'' ,$otpPattern);  ;
        } 
        if (isset($options['otp_validation_pattern'])){
            $otpPattern = $options['otp_validation_pattern'];
        }
        
        $otpErrorMessage = 'You probably made a typo. An easier way avoid mistakes is to use copy - paste.';
        $builder->add('email', EmailType::class, [
                    'label'=>'Email',
                    'required'=>true,
                    'constraints' => [
                        new NotBlank()
                    ]
                ])
                // this is MD5 hash like 'df3971ef535fa21cbcf6131134026226'
                ->add('oneTimePass', TextType::class, [
                    'label'=>'One time password',
                    'required'=>true,
                    'constraints' => [
                        new Regex ([
                            'pattern' => $otpPattern,
                            'message' => $otpErrorMessage
                        ]),
                        new NotBlank([
                            'message'=> $otpErrorMessage
                        ])
                    ]
                ]); 
    }
    
    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver) {
        parent::configureOptions($resolver);
    }
    
    public function getBlockPrefix() {
        return('appbundle_'.parent::getBlockPrefix());
    }
    
}
