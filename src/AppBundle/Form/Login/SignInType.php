<?php

/*
 * The MIT License
 *
 * Copyright 2019 Semyon Mamonov <semyon.mamonov@gmail.com>.
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
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use AppBundle\Entity\User;


/**
 * Description of SignInType
 *
 * Generated: Dec 15, 2019 12:52:25 PM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class SignInType extends AbstractType {
    //put your code here

    
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {

        $builder->add('email', \Symfony\Component\Form\Extension\Core\Type\TextType::class, ['label'=>'Email as user name:'])
                ->add('pass', PasswordType::class, ['label'=>'Password'])
                ->add('_remember_me', CheckboxType::class, ['required'=>false, 'mapped'=>false, 'label'=>'Remember me']);
        
    }
    
    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver) {
        //$resolver->setDefault('data_class', User::class );
    }
    
    public function getBlockPrefix() {
        //return 'appbundle_'.parent::getBlockPrefix();
        return null;
    }
    
}
